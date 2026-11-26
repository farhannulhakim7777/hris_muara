<?php
/**
 * Cek semua user di database untuk melihat opsi login
 */

require_once __DIR__ . '/includes/bootstrap.php';

echo "<!DOCTYPE html><html><head><title>Cek User Database</title></head><body>";
echo "<h1>Daftar User di Database</h1>";

try {
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("SELECT id, username, nama_lengkap, role, is_active FROM users");
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Aktif</th></tr>";
    foreach ($users as $user) {
        $status = $user['is_active'] == 1 ? '✅ Ya' : '❌ Tidak';
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['nama_lengkap']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr>";
    echo "<h2>Saran</h2>";
    echo "<p>Silakan coba login dengan username di atas dan password yang mungkin Anda tahu.</p>";
    echo "<p>Untuk user 'admin', password yang Anda coba (muara@123) tidak cocok dengan hash di database.</p>";
    echo "<p><strong>Mohon tanya kepada pemilik sistem (ayah Anda) password yang benar untuk user admin.</strong></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
