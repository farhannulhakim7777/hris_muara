<?php
/**
 * Migration: Menambahkan kolom GPS dan konfigurasi geofencing pada tabel hr_kantor.
 * Fields:
 * - latitude: DECIMAL(20, 16) NULL
 * - longitude: DECIMAL(20, 16) NULL
 * - attendance_radius: INT UNSIGNED DEFAULT 10
 * - geofence_enabled: TINYINT(1) DEFAULT 0
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

$conn = Database::getInstance()->getConnection();

echo "Running migration: add_gps_fields_to_hr_kantor...\n";

// Cek apakah kolom latitude sudah ada
$res = $conn->query("SHOW COLUMNS FROM hr_kantor LIKE 'latitude'");
if ($res && $res->num_rows > 0) {
    echo "Columns already exist in hr_kantor. Skipping migration.\n";
    exit(0);
}

$sql = "ALTER TABLE hr_kantor 
        ADD COLUMN latitude DECIMAL(20, 16) NULL DEFAULT NULL AFTER alamat,
        ADD COLUMN longitude DECIMAL(20, 16) NULL DEFAULT NULL AFTER latitude,
        ADD COLUMN attendance_radius INT UNSIGNED NOT NULL DEFAULT 10 AFTER longitude,
        ADD COLUMN geofence_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER attendance_radius";

if ($conn->query($sql)) {
    echo "Migration completed successfully. Columns added to hr_kantor.\n";
} else {
    echo "Migration failed: " . $conn->error . "\n";
    exit(1);
}
