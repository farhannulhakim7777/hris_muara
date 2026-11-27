<?php
/**
 * Migration: Menambahkan kolom koordinat GPS, akurasi, dan jarak masuk/keluar pada tabel hr_absensi.
 */

require_once __DIR__ . '/../../includes/bootstrap.php';

$conn = Database::getInstance()->getConnection();

echo "Running migration: add_gps_fields_to_hr_absensi...\n";

$columns = [
    'latitude_masuk'   => "ADD COLUMN latitude_masuk DECIMAL(20, 16) NULL DEFAULT NULL AFTER lokasi_masuk",
    'longitude_masuk'  => "ADD COLUMN longitude_masuk DECIMAL(20, 16) NULL DEFAULT NULL AFTER latitude_masuk",
    'accuracy_masuk'   => "ADD COLUMN accuracy_masuk DECIMAL(10, 2) NULL DEFAULT NULL AFTER longitude_masuk",
    'jarak_masuk'      => "ADD COLUMN jarak_masuk DECIMAL(10, 2) NULL DEFAULT NULL AFTER accuracy_masuk",
    'latitude_keluar'  => "ADD COLUMN latitude_keluar DECIMAL(20, 16) NULL DEFAULT NULL AFTER lokasi_keluar",
    'longitude_keluar' => "ADD COLUMN longitude_keluar DECIMAL(20, 16) NULL DEFAULT NULL AFTER latitude_keluar",
    'accuracy_keluar'  => "ADD COLUMN accuracy_keluar DECIMAL(10, 2) NULL DEFAULT NULL AFTER longitude_keluar",
    'jarak_keluar'     => "ADD COLUMN jarak_keluar DECIMAL(10, 2) NULL DEFAULT NULL AFTER accuracy_keluar",
];

$added = 0;
foreach ($columns as $colName => $alterClause) {
    $res = $conn->query("SHOW COLUMNS FROM hr_absensi LIKE '{$colName}'");
    if ($res && $res->num_rows > 0) {
        echo "Column '{$colName}' already exists in hr_absensi. Skipping.\n";
        continue;
    }

    $sql = "ALTER TABLE hr_absensi {$alterClause}";
    if ($conn->query($sql)) {
        echo "Column '{$colName}' added to hr_absensi.\n";
        $added++;
    } else {
        echo "Failed adding '{$colName}': " . $conn->error . "\n";
        exit(1);
    }
}

echo "Migration completed successfully ({$added} column(s) added).\n";
