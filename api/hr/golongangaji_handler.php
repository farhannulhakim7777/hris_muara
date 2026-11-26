<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/bootstrap.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $user_role = $_SESSION['role'] ?? '';
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data golongan gaji.']);
        exit;
    }
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET') {
        $sql = "SELECT * FROM hr_golongan_gaji ORDER BY nama_golongan ASC";
        $result = $conn->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            if (empty($_POST['nama_golongan'])) {
                throw new Exception("Nama Golongan wajib diisi.");
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nama_golongan = trim($_POST['nama_golongan']);
            $gaji_pokok = !empty($_POST['gaji_pokok']) ? (float)$_POST['gaji_pokok'] : 0;
            $keterangan = $_POST['keterangan'];

            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_golongan_gaji WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    throw new Exception("Golongan gaji dengan ID tersebut tidak ditemukan.");
                }
            }

            $conn->begin_transaction();
            try {
                if ($id) {
                    $stmt = $conn->prepare("UPDATE hr_golongan_gaji SET nama_golongan=?, gaji_pokok=?, keterangan=? WHERE id=?");
                    $stmt->bind_param("sdsi", $nama_golongan, $gaji_pokok, $keterangan, $id);
                    if (!$stmt->execute()) throw new Exception("Gagal mengupdate data: " . $stmt->error);
                    $stmt->close();
                } else {
                    $stmt = $conn->prepare("INSERT INTO hr_golongan_gaji (nama_golongan, gaji_pokok, keterangan) VALUES (?, ?, ?)");
                    $stmt->bind_param("sds", $nama_golongan, $gaji_pokok, $keterangan);
                    if (!$stmt->execute()) throw new Exception("Gagal menyimpan data: " . $stmt->error);
                    $stmt->close();
                }
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data golongan gaji berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) throw new Exception("ID tidak valid.");

            $stmt_check = $conn->prepare("SELECT id FROM hr_golongan_gaji WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                throw new Exception("Golongan gaji tidak ditemukan.");
            }

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_golongan_gaji WHERE id = ?");
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) throw new Exception("Gagal menghapus data: " . $stmt->error);
                if ($stmt->affected_rows === 0) throw new Exception("Tidak ada data yang dihapus.");
                $stmt->close();
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data golongan gaji berhasil dihapus.']);
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
    error_log("Golongan Gaji Handler Error: " . $e->getMessage());
}