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
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $user_role = $_SESSION['role'] ?? '';
    if ($user_role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan operasi ini. Hanya admin yang boleh mengubah data divisi.']);
        exit;
    }
}

$conn = Database::getInstance()->getConnection();

try {
    if ($method === 'GET') {
        $sql = "SELECT * FROM hr_divisi WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($_GET['search'])) {
            $search = "%" . $_GET['search'] . "%";
            $sql .= " AND nama_divisi LIKE ?";
            $params[] = $search;
            $types .= "s";
        }

        $sql .= " ORDER BY nama_divisi ASC";

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
            if (empty($_POST['nama_divisi'])) {
                throw new Exception("Nama Divisi wajib diisi.");
            }

            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nama_divisi = trim($_POST['nama_divisi']);

            // Jika ID dikirim, verifikasi bahwa ID tersebut ada di database
            if ($id) {
                $stmt_check_id = $conn->prepare("SELECT id FROM hr_divisi WHERE id = ?");
                $stmt_check_id->bind_param("i", $id);
                $stmt_check_id->execute();
                $id_exists = $stmt_check_id->get_result()->fetch_assoc();
                $stmt_check_id->close();

                if (!$id_exists) {
                    throw new Exception("Divisi dengan ID tersebut tidak ditemukan.");
                }
            }

            // Mulai transaksi database
            $conn->begin_transaction();

            try {
                if ($id) {
                    // UPDATE
                    $stmt = $conn->prepare("UPDATE hr_divisi SET nama_divisi=? WHERE id=?");
                    $stmt->bind_param("si", $nama_divisi, $id);

                    if (!$stmt->execute()) {
                        throw new Exception("Gagal mengupdate data: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    // INSERT
                    $stmt = $conn->prepare("INSERT INTO hr_divisi (nama_divisi) VALUES (?)");
                    $stmt->bind_param("s", $nama_divisi);

                    if (!$stmt->execute()) {
                        throw new Exception("Gagal menyimpan data: " . $stmt->error);
                    }
                    $stmt->close();
                }

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data divisi berhasil disimpan.']);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

        } elseif ($action === 'delete') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            if (!$id) throw new Exception("ID tidak valid.");

            // Verifikasi divisi ada
            $stmt_check = $conn->prepare("SELECT id FROM hr_divisi WHERE id = ?");
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $exists = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            if (!$exists) {
                throw new Exception("Divisi tidak ditemukan.");
            }

            // Cek apakah divisi sedang digunakan oleh karyawan
            $check = $conn->prepare("SELECT id FROM hr_karyawan WHERE divisi_id = ? LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception("Divisi tidak dapat dihapus karena sedang digunakan oleh data karyawan.");
            }
            $check->close();

            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("DELETE FROM hr_divisi WHERE id = ?");
                $stmt->bind_param("i", $id);

                if (!$stmt->execute()) {
                    throw new Exception("Gagal menghapus data: " . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Tidak ada data yang dihapus.");
                }
                $stmt->close();

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Data divisi berhasil dihapus.']);
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
    error_log("Divisi Handler Error: " . $e->getMessage());
}