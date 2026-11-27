<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/bootstrap.php';

// Cek autentikasi
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Cek otorisasi untuk operasi yang mengubah data (POST)
// Hanya admin yang boleh mengubah data karyawan
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $user_role = $_SESSION['role'] ?? '';
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data karyawan.']);
        exit;
    }
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET' && (!isset($_GET['action']) || $_GET['action'] === 'list')) {
        // Ambil data karyawan dengan filter pencarian dan status
        $sql = "SELECT k.*, j.nama_jabatan, d.nama_divisi, jk.nama_jadwal, kt.nama_kantor, gg.nama_golongan as nama_golongan_gaji, u.username as username_user,
                (SELECT id FROM hr_evaluasi_probation WHERE karyawan_id = k.id ORDER BY created_at DESC LIMIT 1) as latest_probation_evaluation_id,
                (SELECT COUNT(*) FROM hr_evaluasi_probation WHERE karyawan_id = k.id) as probation_history_count,
                (SELECT COUNT(*) FROM hr_riwayat_gaji WHERE karyawan_id = k.id) as salary_history_count
                FROM hr_karyawan k 
                LEFT JOIN hr_jabatan j ON k.jabatan_id = j.id 
                LEFT JOIN hr_jadwal_kerja jk ON k.jadwal_kerja_id = jk.id
                LEFT JOIN hr_divisi d ON k.divisi_id = d.id
                LEFT JOIN hr_kantor kt ON k.kantor_id = kt.id
                LEFT JOIN hr_golongan_gaji gg ON k.golongan_gaji_id = gg.id
                LEFT JOIN users u ON k.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($_GET['search'])) {
            $search = "%" . $_GET['search'] . "%";
            $sql .= " AND (k.nip LIKE ? OR k.nama_lengkap LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $types .= "ss";
        }

        if (!empty($_GET['status'])) {
            $sql .= " AND k.status = ?";
            $params[] = $_GET['status'];
            $types .= "s";
        }

        $sql .= " ORDER BY k.nama_lengkap ASC";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_stats') {
        $sql = "SELECT 
                    COUNT(CASE WHEN status = 'aktif' THEN 1 END) as aktif,
                    COUNT(CASE WHEN status = 'nonaktif' THEN 1 END) as nonaktif,
                    COUNT(CASE WHEN status = 'probation' THEN 1 END) as probation,
                    COUNT(*) as total
                FROM hr_karyawan";
        $result = $conn->query($sql);
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_org_chart_data') {
        // Ambil data untuk struktur organisasi
        // Asumsi: tabel hr_karyawan memiliki kolom atasan_id yang merujuk ke id atasan
        // Jika kolom tersebut belum ada, query ini mungkin perlu disesuaikan atau kolom ditambahkan
        $divisi_id = !empty($_GET['divisi_id']) ? (int)$_GET['divisi_id'] : null;
        
        $sql = "SELECT k.id, k.nama_lengkap, j.nama_jabatan, d.nama_divisi, k.atasan_id, u.foto_profil
                FROM hr_karyawan k
                LEFT JOIN hr_jabatan j ON k.jabatan_id = j.id
                LEFT JOIN hr_divisi d ON k.divisi_id = d.id
                LEFT JOIN users u ON k.user_id = u.id
                WHERE k.status = 'aktif'";
        
        if ($divisi_id) {
            $sql .= " AND k.divisi_id = $divisi_id";
        }
        
        $result = $conn->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_detail') {
        $id = (int)$_GET['id'];
        $sql = "SELECT k.*, j.nama_jabatan, d.nama_divisi, jk.nama_jadwal, kt.nama_kantor, u.foto_profil
                FROM hr_karyawan k 
                LEFT JOIN hr_jabatan j ON k.jabatan_id = j.id 
                LEFT JOIN hr_jadwal_kerja jk ON k.jadwal_kerja_id = jk.id
                LEFT JOIN hr_divisi d ON k.divisi_id = d.id
                LEFT JOIN hr_kantor kt ON k.kantor_id = kt.id
                LEFT JOIN users u ON k.user_id = u.id
                WHERE k.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        
        if ($data) {
            // Inisialisasi default array kosong
            $data['riwayat_jabatan'] = [];
            $data['riwayat_gaji'] = [];

            // Fetch job history
            $stmt_hist = $conn->prepare("
                SELECT rhj.tanggal_mulai, rhj.tanggal_selesai, hj.nama_jabatan 
                FROM hr_riwayat_jabatan rhj
                LEFT JOIN hr_jabatan hj ON rhj.jabatan_id = hj.id
                WHERE rhj.karyawan_id = ?
                ORDER BY rhj.tanggal_mulai DESC
            ");
            if ($stmt_hist) {
                $stmt_hist->bind_param("i", $id);
                if ($stmt_hist->execute()) $data['riwayat_jabatan'] = $stmt_hist->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt_hist->close();

            // Fetch salary history
            $stmt_salary = $conn->prepare("
                SELECT tanggal_perubahan, gaji_lama, gaji_baru, keterangan
                FROM hr_riwayat_gaji
                WHERE karyawan_id = ?
                ORDER BY tanggal_perubahan DESC
            ");
            if ($stmt_salary) {
                $stmt_salary->bind_param("i", $id);
                if ($stmt_salary->execute()) $data['riwayat_gaji'] = $stmt_salary->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt_salary->close();
        }
        
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
        }

    } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_available_users') {
        // Ambil daftar user yang belum terhubung dengan karyawan lain
        // Atau sudah terhubung dengan karyawan yang sedang diedit
        $karyawan_id = !empty($_GET['karyawan_id']) ? (int)$_GET['karyawan_id'] : null;
        
        if ($karyawan_id === null) {
            // MODE ADD: Ambil semua user yang belum terhubung dengan karyawan manapun
            $sql = "SELECT u.id, u.username, u.nama_lengkap, u.role
                    FROM users u
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM hr_karyawan hk
                        WHERE hk.user_id = u.id
                    )
                    ORDER BY u.username ASC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } else {
            // MODE EDIT: Ambil user yang belum terhubung dengan karyawan lain
            // Kecuali user yang sudah terhubung dengan karyawan yang sedang diedit
            $sql = "SELECT u.id, u.username, u.nama_lengkap, u.role
                    FROM users u
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM hr_karyawan hk
                        WHERE hk.user_id = u.id
                        AND hk.id != ?
                    )
                    ORDER BY u.username ASC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $karyawan_id);
            $stmt->execute();
        }
        
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_probation_history') {
        $karyawan_id = (int)$_POST['karyawan_id'];
        
        $sql = "SELECT ep.*, u.nama_lengkap as nama_penilai 
                FROM hr_evaluasi_probation ep
                LEFT JOIN users u ON ep.penilai_id = u.id
                WHERE ep.karyawan_id = ? 
                ORDER BY ep.tanggal_evaluasi DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $karyawan_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_salary_history') {
        $karyawan_id = (int)$_POST['karyawan_id'];
        
        $sql = "SELECT * FROM hr_riwayat_gaji 
                WHERE karyawan_id = ? 
                ORDER BY tanggal_perubahan DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $karyawan_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            // Generate request ID for logging
            $request_id = uniqid('req_');
            
            // Validasi Input
            if (empty($_POST['nip']) || empty($_POST['nama_lengkap'])) {
                error_log("[$request_id] Validation failed: NIP or nama lengkap empty");
                throw new Exception("NIP dan Nama Lengkap wajib diisi.");
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nip = trim($_POST['nip']);
            $nama_lengkap = trim($_POST['nama_lengkap']);
            $jabatan_id = !empty($_POST['jabatan_id']) ? (int)$_POST['jabatan_id'] : null;
            $atasan_id = !empty($_POST['atasan_id']) ? (int)$_POST['atasan_id'] : null;
            $jadwal_kerja_id = !empty($_POST['jadwal_kerja_id']) ? (int)$_POST['jadwal_kerja_id'] : null;
            $divisi_id = !empty($_POST['divisi_id']) ? (int)$_POST['divisi_id'] : null;
            $kantor_id = !empty($_POST['kantor_id']) ? (int)$_POST['kantor_id'] : null;
            $golongan_gaji_id = !empty($_POST['golongan_gaji_id']) ? (int)$_POST['golongan_gaji_id'] : null;
            $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
            // Konversi format tanggal dari DD/MM/YYYY ke YYYY-MM-DD jika diperlukan
            if ($tanggal_lahir && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggal_lahir)) {
                error_log("[$request_id] Converting date from DD/MM/YYYY to YYYY-MM-DD: $tanggal_lahir");
                $date = DateTime::createFromFormat('d/m/Y', $tanggal_lahir);
                if ($date !== false) {
                    $tanggal_lahir = $date->format('Y-m-d');
                    error_log("[$request_id] Converted date: $tanggal_lahir");
                } else {
                    error_log("[$request_id] Date conversion failed: $tanggal_lahir");
                    throw new Exception("Format tanggal lahir tidak valid.");
                }
            }
            $no_hp = !empty($_POST['no_hp']) ? trim($_POST['no_hp']) : null;
            $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
            $tanggal_masuk = !empty($_POST['tanggal_masuk']) ? $_POST['tanggal_masuk'] : null;
            if (!$tanggal_masuk) {
                throw new Exception("Tanggal masuk wajib diisi.");
            }
            $tanggal_berakhir_kontrak = !empty($_POST['tanggal_berakhir_kontrak']) ? $_POST['tanggal_berakhir_kontrak'] : null;
            $status = !empty($_POST['status']) ? $_POST['status'] : 'aktif';
            $npwp = !empty($_POST['npwp']) ? trim($_POST['npwp']) : null;
            $status_ptkp = $_POST['status_ptkp'] ?? 'TK/0';
            $ikut_bpjs_kes = isset($_POST['ikut_bpjs_kes']) ? 1 : 0;
            $ikut_bpjs_tk = isset($_POST['ikut_bpjs_tk']) ? 1 : 0;
            $user_id = !empty($_POST['user_id']) && $_POST['user_id'] !== '0' ? (int)$_POST['user_id'] : null;

            // Validasi tanggal lahir (setelah konversi ke YYYY-MM-DD)
            if ($tanggal_lahir) {
                error_log("[$request_id] Validating tanggal_lahir: $tanggal_lahir");
                $date = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
                if ($date === false) {
                    error_log("[$request_id] Date format invalid after conversion: $tanggal_lahir");
                    throw new Exception("Format tanggal lahir tidak valid.");
                }
                // Check for errors like 31 February
                $errors = DateTime::getLastErrors();
                if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                    error_log("[$request_id] Date calendar error: " . print_r($errors, true));
                    throw new Exception("Tanggal lahir tidak valid.");
                }
                // Tanggal lahir boleh di masa depan, jadi tidak ada validasi timestamp
                error_log("[$request_id] Date validation passed");
            }

            // Validasi nomor HP
            if ($no_hp) {
                if (strlen($no_hp) > 20) {
                    throw new Exception("Nomor HP maksimal 20 karakter.");
                }
                // Validasi format nomor Indonesia: 08xxxxxxxxxx atau +628xxxxxxxxxx
                if (!preg_match('/^(\+62|0)[0-9]{8,20}$/', $no_hp)) {
                    throw new Exception("Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx.");
                }
                // Cek tanda + ganda
                if (substr_count($no_hp, '+') > 1) {
                    throw new Exception("Nomor HP tidak boleh memiliki lebih dari satu tanda +.");
                }
            }

            // Validasi email
            if ($email) {
                if (strlen($email) > 100) {
                    throw new Exception("Email maksimal 100 karakter.");
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Format email tidak valid.");
                }
            }

            // Jika ID dikirim, verifikasi bahwa ID tersebut ada di database
            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_karyawan WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    throw new Exception("Karyawan dengan ID tersebut tidak ditemukan.");
                }
            }

            // Cek NIP unik (kecuali untuk ID sendiri saat edit)
            $sql_check = "SELECT id FROM hr_karyawan WHERE nip = ?";
            $params_check = [$nip];
            $types_check = "s";

            if ($id) {
                $sql_check .= " AND id != ?";
                $params_check[] = $id;
                $types_check .= "i";
            }

            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param($types_check, ...$params_check);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                throw new Exception("NIP sudah digunakan oleh karyawan lain.");
            }
            $stmt_check->close();

            // Validasi user_id jika dipilih
            if ($user_id) {
                // Cek apakah user_id ada di tabel users
                $stmt_user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
                $stmt_user_check->bind_param("i", $user_id);
                $stmt_user_check->execute();
                if ($stmt_user_check->get_result()->num_rows == 0) {
                    $stmt_user_check->close();
                    throw new Exception("Akun pengguna tidak ditemukan.");
                }
                $stmt_user_check->close();

                // Cek apakah user_id sudah terhubung dengan karyawan lain
                $sql_user_check = "SELECT id FROM hr_karyawan WHERE user_id = ?";
                $params_user_check = [$user_id];
                $types_user_check = "i";

                if ($id) {
                    $sql_user_check .= " AND id != ?";
                    $params_user_check[] = $id;
                    $types_user_check .= "i";
                }

                $stmt_user_check2 = $conn->prepare($sql_user_check);
                $stmt_user_check2->bind_param($types_user_check, ...$params_user_check);
                $stmt_user_check2->execute();
                if ($stmt_user_check2->get_result()->num_rows > 0) {
                    $stmt_user_check2->close();
                    throw new Exception("Akun pengguna ini sudah terhubung dengan karyawan lain.");
                }
                $stmt_user_check2->close();
            }

            // Mulai transaksi database untuk operasi INSERT/UPDATE yang aman
            error_log("[$request_id] Starting transaction");
            $conn->begin_transaction();

            try {
                if ($id) {
                    error_log("[$request_id] UPDATE mode: karyawan_id=$id");
                    // UPDATE - Pastikan ID valid dan ditemukan
                    if ($atasan_id == $id) {
                        throw new Exception("Karyawan tidak bisa menjadi atasan untuk dirinya sendiri.");
                    }

                    // Get old jabatan_id before update
                    $stmt_old_jabatan = $conn->prepare("SELECT jabatan_id FROM hr_karyawan WHERE id = ?");
                    $stmt_old_jabatan->bind_param("i", $id);
                    $stmt_old_jabatan->execute();
                    $old_jabatan_result = $stmt_old_jabatan->get_result()->fetch_assoc();
                    $old_jabatan_id = $old_jabatan_result ? $old_jabatan_result['jabatan_id'] : null;
                    $stmt_old_jabatan->close();

                    // Cek perubahan gaji/golongan untuk riwayat
                    $stmt_old = $conn->prepare("SELECT k.golongan_gaji_id, gg.gaji_pokok FROM hr_karyawan k LEFT JOIN hr_golongan_gaji gg ON k.golongan_gaji_id = gg.id WHERE k.id = ?");
                    $stmt_old->bind_param("i", $id);
                    $stmt_old->execute();
                    $old_data = $stmt_old->get_result()->fetch_assoc();
                    $stmt_old->close();

                    // Ambil gaji baru berdasarkan golongan baru
                    $gaji_baru = 0;
                    if ($golongan_gaji_id) {
                        $stmt_new_gaji = $conn->prepare("SELECT gaji_pokok FROM hr_golongan_gaji WHERE id = ?");
                        $stmt_new_gaji->bind_param("i", $golongan_gaji_id);
                        $stmt_new_gaji->execute();
                        $res_new_gaji = $stmt_new_gaji->get_result()->fetch_assoc();
                        if ($res_new_gaji) $gaji_baru = $res_new_gaji['gaji_pokok'];
                        $stmt_new_gaji->close();
                    }

                    $gaji_lama = $old_data['gaji_pokok'] ?? 0;

                    // Jika ada perubahan golongan dan gaji berbeda, catat riwayat
                    if ($old_data && $old_data['golongan_gaji_id'] != $golongan_gaji_id && $gaji_lama != $gaji_baru) {
                        $tgl_perubahan = date('Y-m-d');
                        $ket_perubahan = "Perubahan Golongan Gaji";
                        $stmt_hist = $conn->prepare("INSERT INTO hr_riwayat_gaji (karyawan_id, tanggal_perubahan, gaji_lama, gaji_baru, keterangan) VALUES (?, ?, ?, ?, ?)");
                        $stmt_hist->bind_param("isdds", $id, $tgl_perubahan, $gaji_lama, $gaji_baru, $ket_perubahan);
                        $stmt_hist->execute();
                        $stmt_hist->close();
                    }

                    // Update Data
                    try {
                        $stmt = $conn->prepare("UPDATE hr_karyawan SET nip=?, nama_lengkap=?, email=?, no_hp=?, tanggal_lahir=?, jabatan_id=?, atasan_id=?, jadwal_kerja_id=?, divisi_id=?, kantor_id=?, golongan_gaji_id=?, tanggal_masuk=?, tanggal_berakhir_kontrak=?, user_id=?, status=?, npwp=?, status_ptkp=?, ikut_bpjs_kes=?, ikut_bpjs_tk=? WHERE id=?");
                        $stmt->bind_param("sssssiiiiiississsiii", $nip, $nama_lengkap, $email, $no_hp, $tanggal_lahir, $jabatan_id, $atasan_id, $jadwal_kerja_id, $divisi_id, $kantor_id, $golongan_gaji_id, $tanggal_masuk, $tanggal_berakhir_kontrak, $user_id, $status, $npwp, $status_ptkp, $ikut_bpjs_kes, $ikut_bpjs_tk, $id);

                        if (!$stmt->execute()) {
                            throw new Exception("Gagal mengupdate data: " . $stmt->error);
                        }
                        $stmt->close();
                    } catch (Exception $e) {
                        // Cek duplicate key error (error code 1062)
                        if ($e instanceof mysqli_sql_exception && $e->getCode() == 1062) {
                            throw new Exception("Akun pengguna ini sudah terhubung dengan karyawan lain.");
                        }
                        throw $e;
                    }

                    // After successful update, check if jabatan changed
                    if ($old_jabatan_id != $jabatan_id && $jabatan_id !== null) {
                        // End the previous job history record
                        $stmt_end_hist = $conn->prepare("UPDATE hr_riwayat_jabatan SET tanggal_selesai = CURDATE() WHERE karyawan_id = ? AND tanggal_selesai IS NULL");
                        $stmt_end_hist->bind_param("i", $id);
                        $stmt_end_hist->execute();
                        $stmt_end_hist->close();

                        // Insert new job history record
                        $stmt_new_hist = $conn->prepare("INSERT INTO hr_riwayat_jabatan (karyawan_id, jabatan_id, tanggal_mulai) VALUES (?, ?, CURDATE())");
                        $stmt_new_hist->bind_param("ii", $id, $jabatan_id);
                        $stmt_new_hist->execute();
                        $stmt_new_hist->close();
                    }
                } else {
                    // INSERT - ID kosong, buat data baru
                    error_log("[$request_id] INSERT mode");
                    try {
                        $stmt = $conn->prepare("INSERT INTO hr_karyawan (nip, nama_lengkap, email, no_hp, tanggal_lahir, jabatan_id, atasan_id, jadwal_kerja_id, divisi_id, kantor_id, golongan_gaji_id, tanggal_masuk, tanggal_berakhir_kontrak, user_id, status, npwp, status_ptkp, ikut_bpjs_kes, ikut_bpjs_tk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        error_log("[$request_id] INSERT prepared statement ready");
                        $stmt->bind_param("sssssiiiiiississsii", $nip, $nama_lengkap, $email, $no_hp, $tanggal_lahir, $jabatan_id, $atasan_id, $jadwal_kerja_id, $divisi_id, $kantor_id, $golongan_gaji_id, $tanggal_masuk, $tanggal_berakhir_kontrak, $user_id, $status, $npwp, $status_ptkp, $ikut_bpjs_kes, $ikut_bpjs_tk);
                        error_log("[$request_id] INSERT bind_param done");

                        if (!$stmt->execute()) {
                            $error = $stmt->error;
                            $stmt->close();
                            error_log("[$request_id] INSERT execute failed: $error");
                            throw new Exception("Gagal menyimpan data: " . $error);
                        }
                        $stmt->close();
                        error_log("[$request_id] INSERT execute success");

                        $karyawan_id = $conn->insert_id;
                        // Insert initial job history
                        if ($jabatan_id) {
                            $stmt_hist = $conn->prepare("INSERT INTO hr_riwayat_jabatan (karyawan_id, jabatan_id, tanggal_mulai) VALUES (?, ?, ?)");
                            $stmt_hist->bind_param("iis", $karyawan_id, $jabatan_id, $tanggal_masuk);
                            $stmt_hist->execute();
                            $stmt_hist->close();
                        }
                    } catch (Exception $e) {
                        // Cek duplicate key error (error code 1062)
                        if ($e instanceof mysqli_sql_exception && $e->getCode() == 1062) {
                            throw new Exception("Akun pengguna ini sudah terhubung dengan karyawan lain.");
                        }
                        throw $e;
                    }
                }

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data karyawan berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) throw new Exception("ID tidak valid.");

            // Verifikasi bahwa karyawan ada sebelum menghapus
            $stmt_check = $conn->prepare("SELECT id FROM hr_karyawan WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                throw new Exception("Karyawan tidak ditemukan.");
            }

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_karyawan WHERE id = ?");
                $stmt->bind_param("i", $id);

                if (!$stmt->execute()) {
                    throw new Exception("Gagal menghapus data: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Tidak ada data yang dihapus.");
                }
                $stmt->close();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data karyawan berhasil dihapus.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        }elseif ($action === 'save_probation_evaluation') {
            $karyawan_id = (int)$_POST['karyawan_id'];
            $tanggal_evaluasi = $_POST['tanggal_evaluasi'];
            $skor_teknis = (int)$_POST['skor_teknis'];
            $skor_budaya = (int)$_POST['skor_budaya'];
            $rekomendasi = $_POST['rekomendasi'];
            $catatan = $_POST['catatan'];
            $penilai_id = $_SESSION['user_id'];

            // Verifikasi karyawan ada
            $stmt_check = $conn->prepare("SELECT id FROM hr_karyawan WHERE id = ?");
            $stmt_check->bind_param("i", $karyawan_id);
            $stmt_check->execute();
            $karyawan_exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$karyawan_exists) {
                throw new Exception("Karyawan tidak ditemukan.");
            }

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO hr_evaluasi_probation (karyawan_id, tanggal_evaluasi, penilai_id, skor_teknis, skor_budaya, rekomendasi, catatan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isiisss", $karyawan_id, $tanggal_evaluasi, $penilai_id, $skor_teknis, $skor_budaya, $rekomendasi, $catatan);
                $stmt->execute();
                $stmt->close();

                // Jika rekomendasi adalah angkat tetap atau terminasi, update status karyawan dengan prepared statement
                if ($rekomendasi === 'angkat_tetap') {
                    $stmt_update = $conn->prepare("UPDATE hr_karyawan SET status = 'aktif' WHERE id = ?");
                    $stmt_update->bind_param("i", $karyawan_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                } elseif ($rekomendasi === 'terminasi') {
                    $stmt_update = $conn->prepare("UPDATE hr_karyawan SET status = 'nonaktif' WHERE id = ?");
                    $stmt_update->bind_param("i", $karyawan_id);
                    $stmt_update->execute();
                    $stmt_update->close();

                    // Optional: Nonaktifkan user login juga dengan prepared statement
                    $stmt_user = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = (SELECT user_id FROM hr_karyawan WHERE id = ?)");
                    $stmt_user->bind_param("i", $karyawan_id);
                    $stmt_user->execute();
                    $stmt_user->close();
                }
                // Jika perpanjang_probation, status tetap 'probation', mungkin update tanggal berakhir kontrak jika ada fieldnya

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Evaluasi probation berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } elseif ($action === 'update_atasan') {
            $karyawan_id = (int)$_POST['karyawan_id'];
            $atasan_id = (int)$_POST['atasan_id'];

            if ($karyawan_id === $atasan_id) {
                throw new Exception("Karyawan tidak bisa menjadi atasan untuk dirinya sendiri.");
            }

            // Verifikasi karyawan ada
            $stmt_check = $conn->prepare("SELECT id FROM hr_karyawan WHERE id = ?");
            $stmt_check->bind_param("i", $karyawan_id);
            $stmt_check->execute();
            $karyawan_exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$karyawan_exists) {
                throw new Exception("Karyawan tidak ditemukan.");
            }

            // Jika atasan_id tidak kosong, verifikasi atasan juga ada
            if ($atasan_id) {
                $stmt_check_atasan = $conn->prepare("SELECT id FROM hr_karyawan WHERE id = ?");
                $stmt_check_atasan->bind_param("i", $atasan_id);
                $stmt_check_atasan->execute();
                $atasan_exists = $stmt_check_atasan->get_result()->fetch_assoc();
                $stmt_check_atasan->close();

                if (!$atasan_exists) {
                    throw new Exception("Atasan tidak ditemukan.");
                }
            }

            // TODO: Tambahkan validasi siklus (circular dependency) di sini jika diperlukan

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("UPDATE hr_karyawan SET atasan_id = ? WHERE id = ?");
                $stmt->bind_param("ii", $atasan_id, $karyawan_id);

                if (!$stmt->execute()) {
                    throw new Exception("Gagal memperbarui atasan: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Tidak ada perubahan data atau karyawan tidak ditemukan.");
                }
                $stmt->close();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Atasan berhasil diperbarui.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } else {
            throw new Exception("Action tidak valid.");
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Karyawan Handler Error: " . $e->getMessage());
}