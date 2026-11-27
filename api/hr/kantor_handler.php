<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/bootstrap.php';

// 1. Validasi Autentikasi Session (HTTP 401 jika unauthenticated)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 2. Validasi Otorisasi Administrator untuk mutasi data (HTTP 403 jika bukan admin)
if ($method === 'POST') {
    $user_role = $_SESSION['role'] ?? '';
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data kantor.']);
        exit;
    }

    // 3. Validasi CSRF Token
    verify_csrf_token();
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET') {
        $sql = "SELECT id, nama_kantor, jenis_kantor, alamat, latitude, longitude, attendance_radius, geofence_enabled, created_at, updated_at FROM hr_kantor WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($_GET['search'])) {
            $search = "%" . trim($_GET['search']) . "%";
            $sql .= " AND nama_kantor LIKE ?";
            $params[] = $search;
            $types .= "s";
        }

        $sql .= " ORDER BY nama_kantor ASC";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $data]);
        exit;

    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            // Validasi Nama Kantor
            if (empty(trim($_POST['nama_kantor'] ?? ''))) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nama Kantor wajib diisi.']);
                exit;
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nama_kantor = trim($_POST['nama_kantor']);
            $jenis_kantor = $_POST['jenis_kantor'] ?? 'Cabang';
            if (!in_array($jenis_kantor, ['Pusat', 'Cabang', 'Lainnya'])) {
                $jenis_kantor = 'Cabang';
            }
            $alamat = trim($_POST['alamat'] ?? '');

            // Validasi Geofencing & Radius
            $geofence_enabled = (!empty($_POST['geofence_enabled']) && in_array($_POST['geofence_enabled'], [1, '1', true, 'true', 'on'], true)) ? 1 : 0;
            
            $radius_raw = $_POST['attendance_radius'] ?? 10;
            if (!is_numeric($radius_raw) || (int)$radius_raw <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Radius absensi harus berupa angka positif lebih besar dari nol (dalam meter).']);
                exit;
            }
            $attendance_radius = (int)$radius_raw;

            // Validasi Koordinat GPS
            $lat_raw = trim($_POST['latitude'] ?? '');
            $long_raw = trim($_POST['longitude'] ?? '');

            if ($geofence_enabled === 1 && ($lat_raw === '' || $long_raw === '')) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Koordinat kantor (Latitude dan Longitude) wajib diisi ketika fitur geofencing diaktifkan.']);
                exit;
            }

            $latitude_bind = null;
            if ($lat_raw !== '') {
                if (!is_numeric($lat_raw)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Nilai Latitude harus berupa angka koordinat yang valid.']);
                    exit;
                }
                $lat_float = (float)$lat_raw;
                if ($lat_float < -90 || $lat_float > 90) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Nilai Latitude tidak valid. Harus berada di antara -90 dan 90 derajat.']);
                    exit;
                }
                $latitude_bind = $lat_raw;
            }

            $longitude_bind = null;
            if ($long_raw !== '') {
                if (!is_numeric($long_raw)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Nilai Longitude harus berupa angka koordinat yang valid.']);
                    exit;
                }
                $long_float = (float)$long_raw;
                if ($long_float < -180 || $long_float > 180) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Nilai Longitude tidak valid. Harus berada di antara -180 dan 180 derajat.']);
                    exit;
                }
                $longitude_bind = $long_raw;
            }

            // Cek keberadaan ID jika mode update
            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_kantor WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Kantor dengan ID tersebut tidak ditemukan.']);
                    exit;
                }
            }

            $conn->begin_transaction();
            try {
                if ($id) {
                    $stmt = $conn->prepare("UPDATE hr_kantor SET nama_kantor=?, jenis_kantor=?, alamat=?, latitude=?, longitude=?, attendance_radius=?, geofence_enabled=? WHERE id=?");
                    $stmt->bind_param("sssssiii", $nama_kantor, $jenis_kantor, $alamat, $latitude_bind, $longitude_bind, $attendance_radius, $geofence_enabled, $id);
                    if (!$stmt->execute()) {
                        throw new Exception("Gagal mengupdate data kantor: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $stmt = $conn->prepare("INSERT INTO hr_kantor (nama_kantor, jenis_kantor, alamat, latitude, longitude, attendance_radius, geofence_enabled) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssii", $nama_kantor, $jenis_kantor, $alamat, $latitude_bind, $longitude_bind, $attendance_radius, $geofence_enabled);
                    if (!$stmt->execute()) {
                        throw new Exception("Gagal menyimpan data kantor: " . $stmt->error);
                    }
                    $stmt->close();
                }
                $conn->commit();
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Data kantor berhasil disimpan.']);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID kantor tidak valid.']);
                exit;
            }

            $stmt_check = $conn->prepare("SELECT id FROM hr_kantor WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Kantor tidak ditemukan.']);
                exit;
            }

            $check = $conn->prepare("SELECT id FROM hr_karyawan WHERE kantor_id = ? LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Kantor tidak dapat dihapus karena sedang digunakan oleh data karyawan.']);
                exit;
            }
            $check->close();

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_kantor WHERE id = ?");
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    throw new Exception("Gagal menghapus data: " . $stmt->error);
                }
                if ($stmt->affected_rows === 0) {
                    throw new Exception("Tidak ada data yang dihapus.");
                }
                $stmt->close();
                $conn->commit();
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Data kantor berhasil dihapus.']);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
            exit;
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Kantor Handler Error: " . $e->getMessage());
    exit;
}
