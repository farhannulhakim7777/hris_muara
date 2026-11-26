<?php
// Disable error display, only log errors
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/bootstrap.php';

// Cek autentikasi
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Cek otorisasi untuk operasi yang mengubah data (POST)
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $user_role = $_SESSION['role'] ?? '';
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data komponen gaji.']);
        exit;
    }
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET') {
        $sql = "SELECT * FROM hr_komponen_gaji ORDER BY jenis, nama_komponen ASC";
        $result = $conn->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            if (empty($_POST['nama_komponen'])) {
                throw new Exception("Nama Komponen wajib diisi.");
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nama_komponen = trim($_POST['nama_komponen']);
            $jenis = $_POST['jenis'] ?? 'pendapatan';
            $tipe_hitung = $_POST['tipe_hitung'] ?? 'bulanan';
            $nilai_default = !empty($_POST['nilai_default']) ? (float)$_POST['nilai_default'] : 0;
            $is_default = isset($_POST['is_default']) ? (int)$_POST['is_default'] : 0;

            // Jika ID dikirim, verifikasi bahwa ID tersebut ada di database
            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_komponen_gaji WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    throw new Exception("Komponen gaji dengan ID tersebut tidak ditemukan.");
                }
            }

            // Mulai transaksi database
            $conn->begin_transaction();

            try {
                if ($id) {
                    // UPDATE
                    $stmt = $conn->prepare("UPDATE hr_komponen_gaji SET nama_komponen=?, jenis=?, tipe_hitung=?, nilai_default=?, is_default=? WHERE id=?");
                    $stmt->bind_param("sssdii", $nama_komponen, $jenis, $tipe_hitung, $nilai_default, $is_default, $id);

                    if (!$stmt->execute()) {
                        throw new Exception("Gagal mengupdate data: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    // INSERT
                    $stmt = $conn->prepare("INSERT INTO hr_komponen_gaji (nama_komponen, jenis, tipe_hitung, nilai_default, is_default) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdii", $nama_komponen, $jenis, $tipe_hitung, $nilai_default, $is_default);

                    if (!$stmt->execute()) {
                        throw new Exception("Gagal menyimpan data: " . $stmt->error);
                    }
                    $stmt->close();
                }

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data komponen gaji berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) throw new Exception("ID tidak valid.");

            // Verifikasi komponen ada
            $stmt_check = $conn->prepare("SELECT id FROM hr_komponen_gaji WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                throw new Exception("Komponen gaji tidak ditemukan.");
            }

            // Cek apakah komponen sedang digunakan
            $check = $conn->prepare("SELECT id FROM hr_penggajian_komponen WHERE komponen_id = ? LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception("Komponen tidak dapat dihapus karena sudah digunakan dalam data penggajian.");
            }
            $check->close();

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_komponen_gaji WHERE id = ?");
                $stmt->bind_param("i", $id);

                if (!$stmt->execute()) {
                    throw new Exception("Gagal menghapus data: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Tidak ada data yang dihapus.");
                }
                $stmt->close();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data komponen gaji berhasil dihapus.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } else {
            throw new Exception("Aksi tidak valid.");
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Komponen Gaji Handler Error: " . $e->getMessage());
}