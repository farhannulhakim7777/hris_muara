<?php
/**
 * Script Reset Password Admin
 * Cara pakai: Buka browser dan akses http://localhost/app-hris/reset_password.php
 * Password akan direset menjadi: adminbaru
 */

require_once __DIR__ . '/includes/bootstrap.php';

try {
    $conn = Database::getInstance()->getConnection();

    // Password baru
    $new_password = 'adminbaru';
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password admin
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->bind_param("s", $password_hash);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<!DOCTYPE html><html><head><title>Reset Password</title></head><body>";
        echo "<h1>Password Berhasil Direset!</h1>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password Baru:</strong> <span style='font-size: 24px; color: green; font-weight: bold;'>$new_password</span></p>";
        echo "<p>Silakan <a href='login.php'>klik di sini untuk login</a></p>";
        echo "</body></html>";
    } else {
        echo "<!DOCTYPE html><html><head><title>Error</title></head><body>";
        echo "<h1>User admin tidak ditemukan!</h1>";
        echo "<p>Mungkin username admin berbeda. Cek database.</p>";
        echo "</body></html>";
    }

    $stmt->close();
} catch (Exception $e) {
    echo "<!DOCTYPE html><html><head><title>Error</title></head><body>";
    echo "<h1>Error:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</body></html>";
}
?>
