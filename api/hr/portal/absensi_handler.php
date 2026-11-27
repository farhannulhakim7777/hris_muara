<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/bootstrap.php';

// Batas akurasi GPS (meter); ubah konstanta ini untuk menyesuaikan toleransi
define('MAX_GPS_ACCURACY_M', 150);

// Direktori upload berdasarkan environment
$uploadBase = (($_ENV['DB_NAME'] ?? '') === 'app_hris_test')
    ? PROJECT_ROOT . '/uploads_test/absensi/'
    : PROJECT_ROOT . '/uploads/absensi/';

// 1. Validasi sesi
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    return;
}

$conn    = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];
$method  = $_SERVER['REQUEST_METHOD'];

// ---------------------------------------------------------------------------
// Fungsi Haversine – mengembalikan jarak dalam meter
// ---------------------------------------------------------------------------
function haversine_distance_m(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $R    = 6371000; // radius bumi dalam meter
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a    = sin($dLat / 2) * sin($dLat / 2)
          + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
          * sin($dLng / 2) * sin($dLng / 2);
    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

try {
    // 2. Ambil data karyawan – sertakan status untuk POST; GET tidak perlu kantor_id
    $stmt_k = $conn->prepare('SELECT id, kantor_id, status FROM hr_karyawan WHERE user_id = ?');
    $stmt_k->bind_param('i', $user_id);
    $stmt_k->execute();
    $karyawan = $stmt_k->get_result()->fetch_assoc();
    if (!$karyawan) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Data karyawan tidak ditemukan untuk user ini.']);
        return;
    }
    $karyawan_id = $karyawan['id'];
    $kantor_id   = $karyawan['kantor_id'];

    // =========================================================================
    // GET
    // =========================================================================
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';

        // --- today_status (tidak butuh kantor) ---
        if ($action === 'today_status') {
            $today = date('Y-m-d');
            $stmt  = $conn->prepare('SELECT * FROM hr_absensi WHERE karyawan_id = ? AND tanggal = ?');
            $stmt->bind_param('is', $karyawan_id, $today);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $data]);
            return;
        }

        // --- office_info ---
        if ($action === 'office_info') {
            if (is_null($kantor_id)) {
                http_response_code(200);
                echo json_encode(['success' => true, 'data' => null, 'message' => 'Karyawan belum memiliki penempatan kantor.']);
                return;
            }
            $stmt_o = $conn->prepare(
                'SELECT nama_kantor, alamat, latitude, longitude, attendance_radius, geofence_enabled
                 FROM hr_kantor WHERE id = ?'
            );
            $stmt_o->bind_param('i', $kantor_id);
            $stmt_o->execute();
            $office = $stmt_o->get_result()->fetch_assoc();
            if ($office) {
                $office['max_accuracy'] = MAX_GPS_ACCURACY_M;
            }
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $office]);
            return;
        }

        // --- riwayat (default) – tidak butuh kantor ---
        $bulan = (int)($_GET['bulan'] ?? date('n'));
        $tahun = (int)($_GET['tahun'] ?? date('Y'));
        $sql   = "SELECT a.*, s.badge_class
                  FROM hr_absensi a
                  LEFT JOIN hr_absensi_status s ON a.status = s.nama_status
                  WHERE a.karyawan_id = ?
                    AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                  ORDER BY a.tanggal DESC";
        $stmt  = $conn->prepare($sql);
        $stmt->bind_param('iii', $karyawan_id, $bulan, $tahun);
        $stmt->execute();
        $data  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $data]);
        return;
    }

    // =========================================================================
    // POST
    // =========================================================================
    if ($method === 'POST') {
        verify_csrf_token();

        // POST wajib memiliki kantor
        if (is_null($kantor_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Karyawan belum memiliki penempatan kantor.']);
            return;
        }

        // Tolak karyawan nonaktif
        if ($karyawan['status'] === 'nonaktif') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Akun karyawan Anda berstatus nonaktif.']);
            return;
        }

        // Ambil data kantor
        $stmt_office = $conn->prepare(
            'SELECT nama_kantor, latitude, longitude, attendance_radius, geofence_enabled
             FROM hr_kantor WHERE id = ?'
        );
        $stmt_office->bind_param('i', $kantor_id);
        $stmt_office->execute();
        $office = $stmt_office->get_result()->fetch_assoc();
        if (!$office) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Kantor tidak ditemukan.']);
            return;
        }
        $geofence_enabled = (int)$office['geofence_enabled'];
        $nama_kantor      = $office['nama_kantor'];

        $action = $_POST['action'] ?? '';
        if (empty($action) || !in_array($action, ['clock_in', 'clock_out'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Aksi absensi tidak valid.']);
            return;
        }

        $today        = date('Y-m-d');
        $now          = date('H:i:s');
        $foto_base64  = $_POST['foto'] ?? '';
        $qr_content   = $_POST['qr_content'] ?? '';
        $jenis_absensi = 'manual';

        if (empty($foto_base64) && empty($qr_content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Metode absensi (Foto Selfie atau QR Code) wajib disertakan.']);
            return;
        }

        // --- Validasi QR (tidak diubah) ---
        if (!empty($qr_content)) {
            if (strpos($qr_content, 'SECURE:') === 0) {
                $payload   = substr($qr_content, 7);
                $decoded   = base64_decode($payload, true);
                if ($decoded === false) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'QR Code tidak valid (format base64 salah).']);
                    return;
                }
                $iv_len     = openssl_cipher_iv_length('aes-256-cbc');
                $iv         = substr($decoded, 0, $iv_len);
                $ciphertext = substr($decoded, $iv_len);
                $key        = Config::get('APP_KEY') ?? 'rahasia_perusahaan_hris_secure_key';
                $decrypted  = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
                $qrData     = json_decode($decrypted, true);
                if (!$qrData) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'QR Code tidak valid atau rusak.']);
                    return;
                }
                if (isset($qrData['generated_at']) && (time() - $qrData['generated_at'] > 60)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'QR Code sudah kadaluarsa. Silakan scan ulang.']);
                    return;
                }
            }
            $jenis_absensi = 'qrcode';
        }

        // --- Validasi GPS ---
        // Variabel GPS yang akan disimpan (nullable)
        $gps_lat = null;
        $gps_lng = null;
        $gps_acc = null;
        $gps_jarak = null;

        // Ambil dan validasi GPS dari request
        $raw_lat = $_POST['latitude'] ?? null;
        $raw_lng = $_POST['longitude'] ?? null;
        $raw_acc = $_POST['accuracy'] ?? null;

        $has_gps = ($raw_lat !== null && $raw_lat !== '' &&
                    $raw_lng !== null && $raw_lng !== '' &&
                    $raw_acc !== null && $raw_acc !== '');

        if ($geofence_enabled) {
            // Koordinat kantor wajib tersedia
            if (empty($office['latitude']) || empty($office['longitude'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Koordinat kantor belum dikonfigurasi. Hubungi admin.']);
                return;
            }
            if (empty($office['attendance_radius']) || $office['attendance_radius'] <= 0) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Radius geofence kantor tidak valid. Hubungi admin.']);
                return;
            }

            // GPS wajib ada saat geofence aktif
            if (!$has_gps) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'GPS wajib aktif untuk absensi di kantor ini.']);
                return;
            }

            // Validasi numerik
            foreach (['latitude' => $raw_lat, 'longitude' => $raw_lng, 'accuracy' => $raw_acc] as $key => $val) {
                if (!is_numeric($val) || is_nan((float)$val) || is_infinite((float)$val)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Nilai {$key} tidak valid."]);
                    return;
                }
            }

            $gps_lat = (float)$raw_lat;
            $gps_lng = (float)$raw_lng;
            $gps_acc = (float)$raw_acc;

            // Rentang dunia
            if ($gps_lat < -90 || $gps_lat > 90 || $gps_lng < -180 || $gps_lng > 180) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Koordinat GPS di luar rentang valid.']);
                return;
            }

            // Accuracy
            if ($gps_acc <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Accuracy GPS harus lebih besar dari 0.']);
                return;
            }
            if ($gps_acc > MAX_GPS_ACCURACY_M) {
                $acc_int = (int)round($gps_acc);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => "Akurasi GPS terlalu rendah (±{$acc_int} m). Pindah ke tempat yang sinyal GPS-nya lebih baik."]);
                return;
            }

            // Hitung jarak
            $gps_jarak = haversine_distance_m(
                $gps_lat, $gps_lng,
                (float)$office['latitude'], (float)$office['longitude']
            );
            $radius    = (int)$office['attendance_radius'];

            if ($gps_jarak > $radius) {
                $jarak_int = (int)round($gps_jarak);
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => "Anda berada {$jarak_int} m dari {$nama_kantor} (maks {$radius} m). Pindah lebih dekat ke kantor."
                ]);
                return;
            }
        } elseif ($has_gps) {
            // Geofence nonaktif tapi GPS tersedia – validasi ketat tanpa menolak absen
            $valid_gps = is_numeric($raw_lat) && !is_nan((float)$raw_lat) && !is_infinite((float)$raw_lat)
                && is_numeric($raw_lng) && !is_nan((float)$raw_lng) && !is_infinite((float)$raw_lng)
                && is_numeric($raw_acc) && !is_nan((float)$raw_acc) && !is_infinite((float)$raw_acc);

            if ($valid_gps) {
                $lat_f = (float)$raw_lat;
                $lng_f = (float)$raw_lng;
                $acc_f = (float)$raw_acc;
                if ($lat_f >= -90 && $lat_f <= 90 && $lng_f >= -180 && $lng_f <= 180 && $acc_f > 0) {
                    $gps_lat = $lat_f;
                    $gps_lng = $lng_f;
                    $gps_acc = $acc_f;
                    if (!empty($office['latitude']) && !empty($office['longitude'])) {
                        $gps_jarak = haversine_distance_m(
                            $gps_lat, $gps_lng,
                            (float)$office['latitude'], (float)$office['longitude']
                        );
                    }
                }
            }
        }

        // lokasi_masuk / lokasi_keluar diisi koordinat tervalidasi (atau "0,0" fallback untuk kompatibilitas)
        $lokasi_str = ($gps_lat !== null) ? "{$gps_lat},{$gps_lng}" : trim($_POST['lokasi'] ?? '');
        if (empty($lokasi_str)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Lokasi absensi wajib disertakan.']);
            return;
        }

        // --- Mulai transaksi ---
        $conn->begin_transaction();

        // --- Cek duplikat (FOR UPDATE) ---
        if ($action === 'clock_in') {
            $check = $conn->prepare('SELECT id FROM hr_absensi WHERE karyawan_id = ? AND tanggal = ? FOR UPDATE');
            $check->bind_param('is', $karyawan_id, $today);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $conn->rollback();
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan absen masuk hari ini.']);
                return;
            }
        } else {
            $check = $conn->prepare('SELECT id, jam_keluar FROM hr_absensi WHERE karyawan_id = ? AND tanggal = ? FOR UPDATE');
            $check->bind_param('is', $karyawan_id, $today);
            $check->execute();
            $row = $check->get_result()->fetch_assoc();
            if (!$row) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Anda belum melakukan absen masuk hari ini.']);
                return;
            }
            if (!empty($row['jam_keluar'])) {
                $conn->rollback();
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan absen pulang hari ini.']);
                return;
            }
        }

        // --- Proses foto ---
        $foto_path    = null;
        $fileFullPath = null;
        if (!empty($foto_base64)) {
            if (!is_dir($uploadBase)) {
                mkdir($uploadBase, 0755, true);
            }
            if (!preg_match('#^data:image/(jpeg|jpg|png);base64,(.+)$#i', $foto_base64, $matches)) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Format data gambar tidak valid.']);
                return;
            }
            $declared   = strtolower($matches[1]);
            $image_data = base64_decode($matches[2], true);
            if ($image_data === false) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Gagal decode gambar.']);
                return;
            }
            if (strlen($image_data) > 5 * 1024 * 1024) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ukuran gambar melebihi batas 5 MiB.']);
                return;
            }
            $finfo  = new finfo(FILEINFO_MIME_TYPE);
            $mime   = $finfo->buffer($image_data);
            $image_type = null;
            if ($mime === 'image/jpeg' && in_array($declared, ['jpeg', 'jpg'])) {
                $image_type = 'jpeg';
            } elseif ($mime === 'image/png' && $declared === 'png') {
                $image_type = 'png';
            }
            if ($image_type === null) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'MIME tipe tidak sesuai dengan data gambar.']);
                return;
            }
            if (getimagesizefromstring($image_data) === false) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'File gambar rusak atau tidak dapat dibaca.']);
                return;
            }
            $fileName     = $karyawan_id . '_' . time() . '_' . $action . '_' . bin2hex(random_bytes(6)) . '.' . $image_type;
            $fileFullPath = $uploadBase . $fileName;
            if (file_put_contents($fileFullPath, $image_data) === false) {
                $conn->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file gambar.']);
                return;
            }
            $foto_path = (strpos($uploadBase, '/uploads_test/') !== false)
                ? 'uploads_test/absensi/' . $fileName
                : 'uploads/absensi/' . $fileName;
            if ($jenis_absensi === 'manual') {
                $jenis_absensi = 'selfie';
            }
        }

        // --- INSERT / UPDATE ---
        if ($action === 'clock_in') {
            $stmt = $conn->prepare(
                'INSERT INTO hr_absensi
                 (karyawan_id, tanggal, jam_masuk, status, lokasi_masuk, foto_masuk, jenis_absensi,
                  latitude_masuk, longitude_masuk, accuracy_masuk, jarak_masuk)
                 VALUES (?, ?, ?, "hadir", ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'isssssdddd',
                $karyawan_id, $today, $now, $lokasi_str, $foto_path, $jenis_absensi,
                $gps_lat, $gps_lng, $gps_acc, $gps_jarak
            );
            if ($stmt->execute()) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Berhasil Absen Masuk pada ' . $now]);
                return;
            }
            $conn->rollback();
            throw new Exception('Gagal absen masuk: ' . $stmt->error);
        } else {
            $stmt = $conn->prepare(
                'UPDATE hr_absensi
                 SET jam_keluar = ?, lokasi_keluar = ?, foto_keluar = ?,
                     latitude_keluar = ?, longitude_keluar = ?, accuracy_keluar = ?, jarak_keluar = ?
                 WHERE id = ?'
            );
            $stmt->bind_param(
                'sssddddi',
                $now, $lokasi_str, $foto_path,
                $gps_lat, $gps_lng, $gps_acc, $gps_jarak,
                $row['id']
            );
            if ($stmt->execute()) {
                $conn->commit();
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Berhasil Absen Pulang pada ' . $now]);
                return;
            }
            $conn->rollback();
            throw new Exception('Gagal absen pulang: ' . $stmt->error);
        }
    } // end POST

} catch (Exception $e) {
    if (!empty($fileFullPath) && file_exists($fileFullPath)) {
        @unlink($fileFullPath);
    }
    if (isset($conn)) {
        @$conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    return;
}
?>
