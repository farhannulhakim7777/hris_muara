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
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data kantor.']);
        exit;
    }
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET') {
        $sql = "SELECT * FROM hr_kantor WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($_GET['search'])) {
            $search = "%" . $_GET['search'] . "%";
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
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            if (empty($_POST['nama_kantor'])) {
                throw new Exception("Nama Kantor wajib diisi.");
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nama_kantor = trim($_POST['nama_kantor']);
            $jenis_kantor = $_POST['jenis_kantor'];
            $alamat = $_POST['alamat'];

            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_kantor WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    throw new Exception("Kantor dengan ID tersebut tidak ditemukan.");
                }
            }

            $conn->begin_transaction();
            try {
                if ($id) {
                    $stmt = $conn->prepare("UPDATE hr_kantor SET nama_kantor=?, jenis_kantor=?, alamat=? WHERE id=?");
                    $stmt->bind_param("sssi", $nama_kantor, $jenis_kantor, $alamat, $id);
                    if (!$stmt->execute()) throw new Exception("Gagal mengupdate data: " . $stmt->error);
                    $stmt->close();
                } else {
                    $stmt = $conn->prepare("INSERT INTO hr_kantor (nama_kantor, jenis_kantor, alamat) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $nama_kantor, $jenis_kantor, $alamat);
                    if (!$stmt->execute()) throw new Exception("Gagal menyimpan data: " . $stmt->error);
                    $stmt->close();
                }
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data kantor berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) throw new Exception("ID tidak valid.");

            $stmt_check = $conn->prepare("SELECT id FROM hr_kantor WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                throw new Exception("Kantor tidak ditemukan.");
            }

            $check = $conn->prepare("SELECT id FROM hr_karyawan WHERE kantor_id = ? LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception("Kantor tidak dapat dihapus karena sedang digunakan oleh data karyawan.");
            }
            $check->close();

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_kantor WHERE id = ?");
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) throw new Exception("Gagal menghapus data: " . $stmt->error);
                if ($stmt->affected_rows === 0) throw new Exception("Tidak ada data yang dihapus.");
                $stmt->close();
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data kantor berhasil dihapus.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Kantor Handler Error: " . $e->getMessage());
}