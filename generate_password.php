<?php
// Generate password hash untuk manual update
$new_password = 'adminbaru'; // Ganti password yang diinginkan di sini
$hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "Password: $new_password\n";
echo "Hash: $hash\n";
echo "\nSQL Query untuk phpMyAdmin:\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'admin';\n";
?>
