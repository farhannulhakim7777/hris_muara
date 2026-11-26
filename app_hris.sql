-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2026 at 10:25 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_hris`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `kode_akun` varchar(20) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `tipe_akun` enum('Aset','Liabilitas','Ekuitas','Pendapatan','Beban') NOT NULL,
  `saldo_normal` enum('Debit','Kredit') NOT NULL,
  `cash_flow_category` enum('Operasi','Investasi','Pendanaan') DEFAULT NULL,
  `is_kas` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag jika ini adalah akun kas/bank',
  `saldo_awal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `parent_id`, `kode_akun`, `nama_akun`, `tipe_akun`, `saldo_normal`, `cash_flow_category`, `is_kas`, `saldo_awal`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(100, 1, NULL, '1', 'Aset', 'Aset', 'Debit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(101, 1, 100, '1-1000', 'Aset Lancar', 'Aset', 'Debit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(102, 1, 101, '1-1100', 'Kas dan Setara Kas', 'Aset', 'Debit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(103, 1, 102, '1-1110', 'Kas di Tangan', 'Aset', 'Debit', NULL, 1, '2000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(104, 1, 102, '1-1120', 'Kas di Bank', 'Aset', 'Debit', NULL, 1, '10000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(105, 1, 101, '1-1200', 'Persediaan Barang Dagang', 'Aset', 'Debit', 'Operasi', 0, '15000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(106, 1, 100, '1-2000', 'Aset Tetap', 'Aset', 'Debit', 'Investasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(107, 1, 106, '1-2100', 'Peralatan Toko', 'Aset', 'Debit', 'Investasi', 0, '5000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(108, 1, 106, '1-2101', 'Akum. Penyusutan - Peralatan', 'Aset', 'Kredit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(200, 1, NULL, '2', 'Liabilitas', 'Liabilitas', 'Kredit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(201, 1, 200, '2-1000', 'Liabilitas Jangka Pendek', 'Liabilitas', 'Kredit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(202, 1, 201, '2-1100', 'Utang Dagang', 'Liabilitas', 'Kredit', 'Operasi', 0, '3000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(300, 1, NULL, '3', 'Ekuitas', 'Ekuitas', 'Kredit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(301, 1, 300, '3-1100', 'Simpanan Pokok Anggota', 'Ekuitas', 'Kredit', 'Pendanaan', 0, '20000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(302, 1, 300, '3-1200', 'Simpanan Sukarela Anggota', 'Ekuitas', 'Kredit', 'Pendanaan', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(303, 1, 300, '3-2100', 'SHU Ditahan', 'Ekuitas', 'Kredit', NULL, 0, '9000000.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(400, 1, NULL, '4', 'Pendapatan', 'Pendapatan', 'Kredit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(401, 1, 400, '4-1000', 'Pendapatan Penjualan Barang', 'Pendapatan', 'Kredit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(402, 1, 400, '4-2000', 'Pendapatan Konsinyasi', 'Pendapatan', 'Kredit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(403, 1, 400, '4-9000', 'Laba Pelepasan Aset', 'Pendapatan', 'Kredit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(500, 1, NULL, '5', 'Beban Pokok Penjualan', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(600, 1, NULL, '6', 'Beban Operasional', 'Beban', 'Debit', NULL, 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(601, 1, 600, '6-1100', 'Beban Gaji Karyawan', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(602, 1, 600, '6-1200', 'Beban Listrik & Air', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(603, 1, 600, '6-1300', 'Beban Perlengkapan Toko', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(604, 1, 600, '6-1400', 'Beban Penyusutan - Peralatan', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(605, 1, 600, '6-9000', 'Rugi Pelepasan Aset', 'Beban', 'Debit', 'Operasi', 0, '0.00', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `username`, `action`, `details`, `ip_address`, `timestamp`) VALUES
(1, 'admin', 'Update Pengaturan', 'Pengaturan aplikasi telah diperbarui.', '::1', '2026-11-21 15:19:31'),
(2, 'admin', 'Update Pengaturan', 'Pengaturan aplikasi telah diperbarui.', '::1', '2026-11-21 15:20:28'),
(3, 'admin', 'Update Pengaturan', 'Pengaturan aplikasi telah diperbarui.', '::1', '2026-11-21 15:21:14'),
(4, 'admin', 'Logout', 'User logged out.', '::1', '2026-11-21 15:42:54'),
(5, 'admin', 'Login', 'Login berhasil.', '::1', '2026-11-22 07:47:12'),
(6, 'admin', 'Logout', 'User logged out.', '::1', '2026-11-22 08:18:35'),
(7, 'admin', 'Login', 'Login berhasil.', '::1', '2026-11-22 08:18:46'),
(8, 'admin', 'Logout', 'User logged out.', '::1', '2026-11-22 08:20:10'),
(9, 'admin', 'Login', 'Login berhasil.', '::1', '2026-11-22 08:24:32'),
(10, 'admin', 'Ganti Password', 'Pengguna berhasil mengganti password.', '::1', '2026-11-22 08:24:55'),
(11, 'admin', 'Login', 'Login berhasil.', '::1', '2026-11-22 08:25:15'),
(12, 'admin', 'Logout', 'User logged out.', '::1', '2026-11-22 08:31:51'),
(13, 'admin', 'Login', 'Login berhasil.', '::1', '2026-11-22 08:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `anggaran`
--

CREATE TABLE `anggaran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `periode_tahun` smallint(4) NOT NULL,
  `jumlah_anggaran` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggaran`
--

INSERT INTO `anggaran` (`id`, `user_id`, `account_id`, `periode_tahun`, `jumlah_anggaran`) VALUES
(1, 1, 601, 2026, '18000000.00'),
(2, 1, 602, 2026, '3600000.00'),
(3, 1, 603, 2026, '1200000.00');

-- --------------------------------------------------------

--
-- Table structure for table `consignment_items`
--

CREATE TABLE `consignment_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL COMMENT 'Harga yang harus dibayar ke pemasok',
  `stok_awal` int(11) NOT NULL DEFAULT 0,
  `tanggal_terima` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consignment_items`
--

INSERT INTO `consignment_items` (`id`, `user_id`, `supplier_id`, `nama_barang`, `harga_jual`, `harga_beli`, `stok_awal`, `tanggal_terima`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 1, 'Buku Tulis Sinar Dunia 38 Lbr', '3500.00', '2500.00', 100, '2026-01-05', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(2, 1, 2, 'Seragam SD Merah Putih', '75000.00', '60000.00', 50, '2026-01-05', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_aset` varchar(150) NOT NULL,
  `tanggal_akuisisi` date NOT NULL,
  `harga_perolehan` decimal(15,2) NOT NULL,
  `nilai_residu` decimal(15,2) NOT NULL DEFAULT 0.00,
  `masa_manfaat` int(11) NOT NULL COMMENT 'Dalam tahun',
  `metode_penyusutan` enum('Garis Lurus','Saldo Menurun') NOT NULL DEFAULT 'Garis Lurus',
  `akun_aset_id` int(11) NOT NULL,
  `akun_akumulasi_penyusutan_id` int(11) NOT NULL,
  `akun_beban_penyusutan_id` int(11) NOT NULL,
  `status` enum('Aktif','Dilepas') NOT NULL DEFAULT 'Aktif',
  `tanggal_pelepasan` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger`
--

CREATE TABLE `general_ledger` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL COMMENT 'Quantity for consignment sales',
  `consignment_item_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `nomor_referensi` varchar(50) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ref_id` int(11) NOT NULL COMMENT 'ID dari tabel sumber (transaksi atau jurnal_entries)',
  `ref_type` enum('transaksi','jurnal') NOT NULL COMMENT 'Tabel sumber',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_reconciled` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Belum, 1=Sudah direkonsiliasi',
  `reconciliation_date` date DEFAULT NULL COMMENT 'Tanggal proses rekonsiliasi dilakukan',
  `reconciliation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_absensi`
--

CREATE TABLE `hr_absensi` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_absensi` enum('qrcode','selfie','manual') DEFAULT 'manual',
  `golongan` varchar(50) DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('hadir','izin','sakit','alpa') DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `foto_masuk` varchar(255) DEFAULT NULL,
  `foto_keluar` varchar(255) DEFAULT NULL,
  `lokasi_masuk` varchar(255) DEFAULT NULL,
  `lokasi_keluar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_absensi`
--

INSERT INTO `hr_absensi` (`id`, `karyawan_id`, `tanggal`, `jenis_absensi`, `golongan`, `jam_masuk`, `jam_keluar`, `status`, `keterangan`, `foto_masuk`, `foto_keluar`, `lokasi_masuk`, `lokasi_keluar`, `created_at`) VALUES
(1, 1, '2026-11-09', 'manual', 'Non-Shift', '08:05:12', '17:09:00', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(2, 1, '2026-11-10', 'manual', 'Non-Shift', '07:59:58', '17:19:43', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(3, 1, '2026-11-11', 'manual', 'Non-Shift', '08:04:04', '17:57:45', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(4, 1, '2026-11-12', 'manual', 'Non-Shift', '07:47:57', '17:33:20', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(5, 1, '2026-11-13', 'manual', 'Non-Shift', '07:55:09', '17:02:42', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(6, 1, '2026-11-16', 'manual', 'Non-Shift', NULL, NULL, 'izin', 'Cuti Disetujui: Liburan keluarga', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(7, 1, '2026-11-17', 'manual', 'Non-Shift', NULL, NULL, 'izin', 'Cuti Disetujui: Liburan keluarga', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(8, 1, '2026-11-18', 'manual', 'Non-Shift', NULL, NULL, 'izin', 'Cuti Disetujui: Liburan keluarga', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(9, 1, '2026-11-19', 'manual', 'Non-Shift', '08:08:26', '17:52:19', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(10, 1, '2026-11-20', 'manual', 'Non-Shift', '07:50:03', '17:28:11', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(11, 2, '2026-11-09', 'manual', 'Non-Shift', '07:53:45', '17:56:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(12, 2, '2026-11-10', 'manual', 'Non-Shift', '08:03:00', '17:19:02', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(13, 2, '2026-11-11', 'manual', 'Non-Shift', '08:09:48', '17:36:37', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(14, 2, '2026-11-12', 'manual', 'Non-Shift', '07:53:26', '17:19:48', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(15, 2, '2026-11-13', 'manual', 'Non-Shift', '07:54:07', '17:36:48', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(16, 2, '2026-11-16', 'manual', 'Non-Shift', '08:12:25', '17:21:00', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(17, 2, '2026-11-17', 'manual', 'Non-Shift', '07:51:12', '17:48:55', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(18, 2, '2026-11-18', 'manual', 'Non-Shift', '07:56:40', '17:07:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(19, 2, '2026-11-19', 'manual', 'Non-Shift', NULL, NULL, 'izin', 'Cuti Disetujui: Demam, surat dokter terlampir', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(20, 2, '2026-11-20', 'manual', 'Non-Shift', '07:54:33', '17:33:22', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(21, 3, '2026-11-09', 'manual', 'Non-Shift', '08:11:02', '17:51:32', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(22, 3, '2026-11-10', 'manual', 'Non-Shift', '08:01:05', '17:56:45', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(23, 3, '2026-11-11', 'manual', 'Non-Shift', '08:13:27', '17:28:54', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(24, 3, '2026-11-12', 'manual', 'Non-Shift', '07:59:58', '17:42:55', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(25, 3, '2026-11-13', 'manual', 'Non-Shift', '07:48:39', '17:15:22', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(26, 3, '2026-11-16', 'manual', 'Non-Shift', '07:52:38', '17:21:56', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(27, 3, '2026-11-17', 'manual', 'Non-Shift', '07:48:59', '17:38:04', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(28, 3, '2026-11-18', 'manual', 'Non-Shift', '07:55:43', '17:06:49', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(29, 3, '2026-11-19', 'manual', 'Non-Shift', '07:47:03', '17:59:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(30, 3, '2026-11-20', 'manual', 'Non-Shift', '07:59:39', '17:14:07', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(31, 4, '2026-11-09', 'manual', 'Non-Shift', '07:54:33', '17:48:29', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(32, 4, '2026-11-10', 'manual', 'Non-Shift', '08:13:14', '17:23:41', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(33, 4, '2026-11-11', 'manual', 'Non-Shift', '08:10:18', '17:56:30', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(34, 4, '2026-11-12', 'manual', 'Non-Shift', '07:47:18', '17:52:23', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(35, 4, '2026-11-13', 'manual', 'Non-Shift', '07:56:42', '17:20:30', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(36, 4, '2026-11-16', 'manual', 'Non-Shift', '08:00:50', '17:26:55', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(37, 4, '2026-11-17', 'manual', 'Non-Shift', '07:48:37', '17:32:24', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(38, 4, '2026-11-18', 'manual', 'Non-Shift', '07:53:45', '17:23:12', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(39, 4, '2026-11-19', 'manual', 'Non-Shift', '08:13:38', '17:42:36', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(40, 4, '2026-11-20', 'manual', 'Non-Shift', '08:00:01', '17:04:01', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(41, 5, '2026-11-09', 'manual', 'Non-Shift', '08:00:08', '17:20:14', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(42, 5, '2026-11-10', 'manual', 'Non-Shift', '08:13:16', '17:18:47', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(43, 5, '2026-11-11', 'manual', 'Non-Shift', '08:03:07', '17:45:37', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(44, 5, '2026-11-12', 'manual', 'Non-Shift', '08:09:46', '17:59:00', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(45, 5, '2026-11-13', 'manual', 'Non-Shift', '08:04:11', '17:37:27', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(46, 5, '2026-11-16', 'manual', 'Non-Shift', '07:52:37', '17:16:28', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(47, 5, '2026-11-17', 'manual', 'Non-Shift', '08:00:53', '17:30:33', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(48, 5, '2026-11-18', 'manual', 'Non-Shift', '08:14:53', '17:02:11', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(49, 5, '2026-11-19', 'manual', 'Non-Shift', '08:03:17', '17:48:53', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(50, 5, '2026-11-20', 'manual', 'Non-Shift', '07:52:19', '17:50:07', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(51, 6, '2026-11-09', 'manual', 'Non-Shift', '07:49:22', '17:19:49', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(52, 6, '2026-11-10', 'manual', 'Non-Shift', '08:00:38', '17:54:31', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(53, 6, '2026-11-11', 'manual', 'Non-Shift', '07:46:10', '17:59:32', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(54, 6, '2026-11-12', 'manual', 'Non-Shift', '08:02:02', '17:59:39', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(55, 6, '2026-11-13', 'manual', 'Non-Shift', '08:00:32', '17:09:34', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(56, 6, '2026-11-16', 'manual', 'Non-Shift', '08:08:25', '17:15:36', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(57, 6, '2026-11-17', 'manual', 'Non-Shift', '08:05:47', '17:35:31', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(58, 6, '2026-11-18', 'manual', 'Non-Shift', '08:08:26', '17:40:50', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(59, 6, '2026-11-19', 'manual', 'Non-Shift', '08:07:14', '17:46:47', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(60, 6, '2026-11-20', 'manual', 'Non-Shift', '07:53:17', '17:36:04', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(61, 7, '2026-11-09', 'manual', 'Non-Shift', '07:55:55', '17:09:10', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(62, 7, '2026-11-10', 'manual', 'Non-Shift', '08:05:36', '17:14:34', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(63, 7, '2026-11-11', 'manual', 'Non-Shift', '07:47:16', '17:12:56', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(64, 7, '2026-11-12', 'manual', 'Non-Shift', '07:51:27', '17:52:14', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(65, 7, '2026-11-13', 'manual', 'Non-Shift', '08:09:02', '17:14:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(66, 7, '2026-11-16', 'manual', 'Non-Shift', '07:51:51', '17:37:09', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:14'),
(67, 7, '2026-11-17', 'manual', 'Non-Shift', '07:57:28', '17:50:49', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(68, 7, '2026-11-18', 'manual', 'Non-Shift', '07:46:23', '17:32:57', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(69, 7, '2026-11-19', 'manual', 'Non-Shift', '08:14:39', '17:10:11', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(70, 7, '2026-11-20', 'manual', 'Non-Shift', '07:52:29', '17:27:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(71, 8, '2026-11-09', 'manual', 'Non-Shift', '08:02:57', '17:42:29', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(72, 8, '2026-11-10', 'manual', 'Non-Shift', '08:08:34', '17:22:21', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(73, 8, '2026-11-11', 'manual', 'Non-Shift', '08:09:36', '17:16:50', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(74, 8, '2026-11-12', 'manual', 'Non-Shift', '07:54:31', '17:02:02', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(75, 8, '2026-11-13', 'manual', 'Non-Shift', '07:59:54', '17:57:49', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(76, 8, '2026-11-16', 'manual', 'Non-Shift', '08:04:36', '17:13:40', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(77, 8, '2026-11-17', 'manual', 'Non-Shift', '08:12:07', '17:15:20', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(78, 8, '2026-11-18', 'manual', 'Non-Shift', '08:06:23', '17:17:48', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(79, 8, '2026-11-19', 'manual', 'Non-Shift', '07:59:26', '17:47:51', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(80, 8, '2026-11-20', 'manual', 'Non-Shift', '07:48:03', '17:58:55', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(81, 10, '2026-11-09', 'manual', 'Non-Shift', '07:48:55', '17:34:31', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(82, 10, '2026-11-10', 'manual', 'Non-Shift', '07:50:42', '17:15:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(83, 10, '2026-11-11', 'manual', 'Non-Shift', '07:58:13', '17:27:14', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(84, 10, '2026-11-12', 'manual', 'Non-Shift', '07:52:48', '17:35:03', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(85, 10, '2026-11-13', 'manual', 'Non-Shift', '07:57:33', '17:57:51', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(86, 10, '2026-11-16', 'manual', 'Non-Shift', '07:59:04', '17:27:32', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(87, 10, '2026-11-17', 'manual', 'Non-Shift', '08:11:26', '17:00:59', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(88, 10, '2026-11-18', 'manual', 'Non-Shift', '07:58:29', '17:21:25', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(89, 10, '2026-11-19', 'manual', 'Non-Shift', '08:09:43', '17:03:00', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(90, 10, '2026-11-20', 'manual', 'Non-Shift', '08:03:14', '17:09:28', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(91, 11, '2026-11-09', 'manual', 'Non-Shift', '08:11:27', '17:04:50', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(92, 11, '2026-11-10', 'manual', 'Non-Shift', '07:56:34', '17:27:38', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(93, 11, '2026-11-11', 'manual', 'Non-Shift', '07:56:09', '17:38:51', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(94, 11, '2026-11-12', 'manual', 'Non-Shift', '08:08:30', '17:20:51', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(95, 11, '2026-11-13', 'manual', 'Non-Shift', '08:07:49', '17:31:10', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(96, 11, '2026-11-16', 'manual', 'Non-Shift', '08:10:05', '17:41:02', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(97, 11, '2026-11-17', 'manual', 'Non-Shift', '07:51:40', '17:21:52', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(98, 11, '2026-11-18', 'manual', 'Non-Shift', '07:51:29', '17:16:02', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(99, 11, '2026-11-19', 'manual', 'Non-Shift', '07:58:08', '17:33:56', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(100, 11, '2026-11-20', 'manual', 'Non-Shift', '07:53:41', '17:51:38', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(101, 12, '2026-11-09', 'manual', 'Non-Shift', '07:45:00', '17:00:37', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(102, 12, '2026-11-10', 'manual', 'Non-Shift', '07:46:46', '17:17:35', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(103, 12, '2026-11-11', 'manual', 'Non-Shift', '07:47:55', '17:00:42', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(104, 12, '2026-11-12', 'manual', 'Non-Shift', '07:50:36', '17:01:56', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(105, 12, '2026-11-13', 'manual', 'Non-Shift', '08:10:16', '17:13:36', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(106, 12, '2026-11-16', 'manual', 'Non-Shift', '08:14:41', '17:59:17', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(107, 12, '2026-11-17', 'manual', 'Non-Shift', '08:01:22', '17:57:11', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(108, 12, '2026-11-18', 'manual', 'Non-Shift', '07:52:18', '17:41:39', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(109, 12, '2026-11-19', 'manual', 'Non-Shift', '07:51:29', '17:34:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(110, 12, '2026-11-20', 'manual', 'Non-Shift', '07:56:28', '17:34:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(111, 13, '2026-11-09', 'manual', 'Non-Shift', '07:54:32', '17:41:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(112, 13, '2026-11-10', 'manual', 'Non-Shift', '07:48:33', '17:06:53', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(113, 13, '2026-11-11', 'manual', 'Non-Shift', '08:14:30', '17:40:46', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(114, 13, '2026-11-12', 'manual', 'Non-Shift', '08:02:21', '17:40:07', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(115, 13, '2026-11-13', 'manual', 'Non-Shift', '08:03:34', '17:53:47', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(116, 13, '2026-11-16', 'manual', 'Non-Shift', '07:49:11', '17:10:29', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(117, 13, '2026-11-17', 'manual', 'Non-Shift', '07:58:24', '17:44:23', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(118, 13, '2026-11-18', 'manual', 'Non-Shift', '07:58:05', '17:00:37', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(119, 13, '2026-11-19', 'manual', 'Non-Shift', '08:02:38', '17:45:49', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(120, 13, '2026-11-20', 'manual', 'Non-Shift', '07:57:32', '17:13:18', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(121, 14, '2026-11-09', 'manual', 'Non-Shift', '08:04:19', '17:17:31', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(122, 14, '2026-11-10', 'manual', 'Non-Shift', '08:08:01', '17:25:48', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(123, 14, '2026-11-11', 'manual', 'Non-Shift', '07:57:10', '17:29:19', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(124, 14, '2026-11-12', 'manual', 'Non-Shift', '08:06:59', '17:11:40', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(125, 14, '2026-11-13', 'manual', 'Non-Shift', '08:09:44', '17:40:13', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(126, 14, '2026-11-16', 'manual', 'Non-Shift', '07:53:38', '17:17:58', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(127, 14, '2026-11-17', 'manual', 'Non-Shift', '08:05:19', '17:35:17', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(128, 14, '2026-11-18', 'manual', 'Non-Shift', '08:09:32', '17:11:07', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(129, 14, '2026-11-19', 'manual', 'Non-Shift', '07:59:36', '17:08:47', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(130, 14, '2026-11-20', 'manual', 'Non-Shift', '08:08:56', '17:08:36', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(131, 15, '2026-11-09', 'manual', 'Non-Shift', '07:49:36', '17:42:54', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(132, 15, '2026-11-10', 'manual', 'Non-Shift', '08:12:14', '17:10:13', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(133, 15, '2026-11-11', 'manual', 'Non-Shift', '07:46:24', '17:23:06', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(134, 15, '2026-11-12', 'manual', 'Non-Shift', '07:52:10', '17:38:01', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(135, 15, '2026-11-13', 'manual', 'Non-Shift', '07:47:41', '17:12:38', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(136, 15, '2026-11-16', 'manual', 'Non-Shift', '08:09:24', '17:42:42', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(137, 15, '2026-11-17', 'manual', 'Non-Shift', '07:52:46', '17:05:08', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(138, 15, '2026-11-18', 'manual', 'Non-Shift', '07:56:40', '17:19:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(139, 15, '2026-11-19', 'manual', 'Non-Shift', '07:54:46', '17:21:50', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(140, 15, '2026-11-20', 'manual', 'Non-Shift', '08:04:41', '17:13:11', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(141, 16, '2026-11-09', 'manual', 'Non-Shift', '07:46:36', '17:05:05', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(142, 16, '2026-11-10', 'manual', 'Non-Shift', '07:57:01', '17:12:36', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(143, 16, '2026-11-11', 'manual', 'Non-Shift', '07:45:59', '17:26:32', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(144, 16, '2026-11-12', 'manual', 'Non-Shift', '07:59:05', '17:23:05', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(145, 16, '2026-11-13', 'manual', 'Non-Shift', '07:45:43', '17:44:22', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(146, 16, '2026-11-16', 'manual', 'Non-Shift', '07:59:27', '17:32:18', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(147, 16, '2026-11-17', 'manual', 'Non-Shift', '08:10:56', '17:28:04', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(148, 16, '2026-11-18', 'manual', 'Non-Shift', '08:14:41', '17:36:57', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(149, 16, '2026-11-19', 'manual', 'Non-Shift', '08:10:08', '17:59:02', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(150, 16, '2026-11-20', 'manual', 'Non-Shift', '07:51:09', '17:06:13', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(151, 9, '2026-11-09', 'manual', 'Non-Shift', '08:13:20', '17:12:06', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(152, 9, '2026-11-10', 'manual', 'Non-Shift', '07:57:57', '17:14:24', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(153, 9, '2026-11-11', 'manual', 'Non-Shift', '07:53:40', '17:51:44', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(154, 9, '2026-11-12', 'manual', 'Non-Shift', '08:11:01', '17:20:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(155, 9, '2026-11-13', 'manual', 'Non-Shift', '07:56:12', '17:15:56', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(156, 9, '2026-11-16', 'manual', 'Non-Shift', '08:14:40', '17:54:22', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(157, 9, '2026-11-17', 'manual', 'Non-Shift', '07:51:17', '17:44:47', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(158, 9, '2026-11-18', 'manual', 'Non-Shift', '07:45:45', '17:58:04', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(159, 9, '2026-11-19', 'manual', 'Non-Shift', '07:51:05', '17:00:18', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15'),
(160, 9, '2026-11-20', 'manual', 'Non-Shift', '07:51:52', '17:09:15', 'hadir', '', NULL, NULL, NULL, NULL, '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_absensi_golongan`
--

CREATE TABLE `hr_absensi_golongan` (
  `id` int(11) NOT NULL,
  `nama_golongan` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_absensi_golongan`
--

INSERT INTO `hr_absensi_golongan` (`id`, `nama_golongan`, `is_active`) VALUES
(1, 'Shift Pagi', 1),
(2, 'Shift Siang', 1),
(3, 'Shift Malam', 1),
(4, 'Non-Shift', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hr_absensi_status`
--

CREATE TABLE `hr_absensi_status` (
  `id` int(11) NOT NULL,
  `nama_status` varchar(50) NOT NULL,
  `badge_class` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_absensi_status`
--

INSERT INTO `hr_absensi_status` (`id`, `nama_status`, `badge_class`, `is_active`) VALUES
(1, 'hadir', 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 1),
(2, 'izin', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 1),
(3, 'sakit', 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200', 1),
(4, 'alpa', 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hr_divisi`
--

CREATE TABLE `hr_divisi` (
  `id` int(11) NOT NULL,
  `nama_divisi` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_divisi`
--

INSERT INTO `hr_divisi` (`id`, `nama_divisi`, `created_at`, `updated_at`) VALUES
(1, 'IT Development', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'Human Resources', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'Finance & Accounting', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(4, 'Marketing', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(5, 'Operasional', '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_dokumen_karyawan`
--

CREATE TABLE `hr_dokumen_karyawan` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `jenis_dokumen` varchar(50) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tanggal_kadaluarsa` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_dokumen_karyawan`
--

INSERT INTO `hr_dokumen_karyawan` (`id`, `karyawan_id`, `jenis_dokumen`, `nama_file`, `path_file`, `tanggal_kadaluarsa`, `created_at`) VALUES
(1, 9, 'KTP', 'ktp_budi.pdf', 'dummy/path', NULL, '2026-11-21 20:42:14'),
(2, 9, 'Kontrak Kerja', 'kontrak_budi_2023.pdf', 'dummy/path', '2027-05-21', '2026-11-21 20:42:14'),
(3, 7, 'Ijazah', 'ijazah_s1_siti.pdf', 'dummy/path', NULL, '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_dokumen_perusahaan`
--

CREATE TABLE `hr_dokumen_perusahaan` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_dokumen_perusahaan`
--

INSERT INTO `hr_dokumen_perusahaan` (`id`, `judul`, `kategori`, `file_path`, `created_at`) VALUES
(1, 'Peraturan Perusahaan 2024', 'Peraturan', '#', '2026-11-21 20:42:14'),
(2, 'Kebijakan Cuti & Absensi', 'Kebijakan', '#', '2026-11-21 20:42:14'),
(3, 'Panduan Klaim Reimbursement', 'Panduan', '#', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_evaluasi_probation`
--

CREATE TABLE `hr_evaluasi_probation` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal_evaluasi` date NOT NULL,
  `penilai_id` int(11) NOT NULL,
  `skor_teknis` int(3) NOT NULL COMMENT 'Skor 1-100',
  `skor_budaya` int(3) NOT NULL COMMENT 'Skor 1-100',
  `rekomendasi` enum('angkat_tetap','perpanjang_probation','terminasi') NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_evaluasi_probation`
--

INSERT INTO `hr_evaluasi_probation` (`id`, `karyawan_id`, `tanggal_evaluasi`, `penilai_id`, `skor_teknis`, `skor_budaya`, `rekomendasi`, `catatan`, `created_at`) VALUES
(1, 16, '2026-09-22', 1, 75, 80, 'perpanjang_probation', 'Kinerja teknis perlu ditingkatkan sedikit lagi.', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_golongan_gaji`
--

CREATE TABLE `hr_golongan_gaji` (
  `id` int(11) NOT NULL,
  `nama_golongan` varchar(100) NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_golongan_gaji`
--

INSERT INTO `hr_golongan_gaji` (`id`, `nama_golongan`, `gaji_pokok`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Grade A', '12000000.00', 'Executive Level', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'Grade B', '8000000.00', 'Managerial Level', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'Grade C', '6000000.00', 'Senior Level', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(4, 'Grade D', '4500000.00', 'Junior Level', '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_jabatan`
--

CREATE TABLE `hr_jabatan` (
  `id` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_jabatan`
--

INSERT INTO `hr_jabatan` (`id`, `nama_jabatan`, `tunjangan`, `created_at`, `updated_at`) VALUES
(1, 'Manager', '2500000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'Supervisor', '1500000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'Senior Staff', '800000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(4, 'Staff', '500000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(5, 'Admin', '300000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(6, 'Komisaris Utama', '10000000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(7, 'Direktur Utama', '8000000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(8, 'Direktur Keuangan', '7000000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(9, 'Direktur HR', '7000000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(10, 'Office Boy', '150000.00', '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_jadwal_kerja`
--

CREATE TABLE `hr_jadwal_kerja` (
  `id` int(11) NOT NULL,
  `nama_jadwal` varchar(100) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_jadwal_kerja`
--

INSERT INTO `hr_jadwal_kerja` (`id`, `nama_jadwal`, `jam_masuk`, `jam_pulang`, `is_active`) VALUES
(1, 'Normal (08-17)', '08:00:00', '17:00:00', 1),
(2, 'Shift Pagi (06-14)', '06:00:00', '14:00:00', 1),
(3, 'Shift Siang (14-22)', '14:00:00', '22:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hr_jatah_cuti`
--

CREATE TABLE `hr_jatah_cuti` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `jatah_awal` int(3) NOT NULL DEFAULT 12,
  `sisa_jatah` int(3) NOT NULL DEFAULT 12
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_jatah_cuti`
--

INSERT INTO `hr_jatah_cuti` (`id`, `karyawan_id`, `tahun`, `jatah_awal`, `sisa_jatah`) VALUES
(1, 1, 2026, 12, 9),
(2, 2, 2026, 12, 12),
(3, 3, 2026, 12, 12),
(4, 4, 2026, 12, 12),
(5, 5, 2026, 12, 12),
(6, 6, 2026, 12, 12),
(7, 7, 2026, 12, 12),
(8, 8, 2026, 12, 12),
(9, 10, 2026, 12, 12),
(10, 11, 2026, 12, 12),
(11, 12, 2026, 12, 12),
(12, 13, 2026, 12, 12),
(13, 14, 2026, 12, 12),
(14, 15, 2026, 12, 12),
(15, 16, 2026, 12, 12),
(16, 9, 2026, 12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `hr_jenis_cuti`
--

CREATE TABLE `hr_jenis_cuti` (
  `id` int(11) NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `mengurangi_jatah_cuti` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Ya, 0=Tidak',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_jenis_cuti`
--

INSERT INTO `hr_jenis_cuti` (`id`, `nama_jenis`, `mengurangi_jatah_cuti`, `is_active`) VALUES
(1, 'Cuti Tahunan', 1, 1),
(2, 'Cuti Sakit', 0, 1),
(3, 'Cuti Menikah', 0, 1),
(4, 'Cuti Melahirkan', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hr_jenis_klaim`
--

CREATE TABLE `hr_jenis_klaim` (
  `id` int(11) NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `max_plafon` decimal(15,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_jenis_klaim`
--

INSERT INTO `hr_jenis_klaim` (`id`, `nama_jenis`, `max_plafon`, `keterangan`) VALUES
(1, 'Kesehatan (Rawat Jalan)', '5000000.00', NULL),
(2, 'Transportasi / Bensin', '1000000.00', NULL),
(3, 'Kacamata', '2000000.00', NULL),
(4, 'Entertainment', '0.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hr_kantor`
--

CREATE TABLE `hr_kantor` (
  `id` int(11) NOT NULL,
  `nama_kantor` varchar(100) NOT NULL,
  `jenis_kantor` enum('Pusat','Cabang','Lainnya') NOT NULL DEFAULT 'Cabang',
  `alamat` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_kantor`
--

INSERT INTO `hr_kantor` (`id`, `nama_kantor`, `jenis_kantor`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Kantor Pusat', 'Pusat', 'Jl. Jend. Sudirman No. Kav 1, Jakarta', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'Cabang Bandung', 'Cabang', 'Jl. Asia Afrika No. 10, Bandung', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'Cabang Surabaya', 'Cabang', 'Jl. Tunjungan No. 5, Surabaya', '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_karyawan`
--

CREATE TABLE `hr_karyawan` (
  `id` int(11) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jabatan_id` int(11) DEFAULT NULL,
  `atasan_id` int(11) DEFAULT NULL,
  `jadwal_kerja_id` int(11) DEFAULT NULL,
  `divisi_id` int(11) DEFAULT NULL,
  `kantor_id` int(11) DEFAULT NULL,
  `golongan_gaji_id` int(11) DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_berakhir_kontrak` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('aktif','nonaktif','probation','kontrak') DEFAULT 'aktif',
  `npwp` varchar(20) DEFAULT NULL,
  `status_ptkp` varchar(5) DEFAULT 'TK/0',
  `ikut_bpjs_kes` tinyint(1) DEFAULT 0,
  `ikut_bpjs_tk` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_karyawan`
--

INSERT INTO `hr_karyawan` (`id`, `nip`, `nama_lengkap`, `email`, `no_hp`, `tanggal_lahir`, `jabatan_id`, `atasan_id`, `jadwal_kerja_id`, `divisi_id`, `kantor_id`, `golongan_gaji_id`, `tanggal_masuk`, `tanggal_berakhir_kontrak`, `user_id`, `status`, `npwp`, `status_ptkp`, `ikut_bpjs_kes`, `ikut_bpjs_tk`, `created_at`, `updated_at`) VALUES
(1, 'KOM001', 'Haryono Subianto', NULL, NULL, '1999-07-22', 6, NULL, 1, NULL, 1, 1, '2023-11-21', '2027-09-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'DIR001', 'Ahmad Zulkifli', NULL, NULL, '1996-12-01', 7, 1, 1, NULL, 2, 2, '2025-11-21', '2027-02-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'DIR002', 'Kartika Sari', NULL, NULL, '1985-12-04', 8, 2, 1, 3, 3, 3, '2022-11-21', '2028-09-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(4, 'DIR003', 'Bambang Wijoyo', NULL, NULL, '1982-10-04', 9, 2, 1, 2, 1, 4, '2022-11-21', '2028-03-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(5, 'MGR001', 'Rina Marlina', NULL, NULL, '2000-11-18', 1, 2, 1, 1, 2, 1, '2021-11-21', '2027-02-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(6, 'MGR002', 'Andi Wijaya', NULL, NULL, '1989-06-02', 1, 2, 1, 4, 3, 2, '2023-11-21', '2028-01-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(7, 'SPV001', 'Siti Aminah', NULL, NULL, '1986-08-26', 2, 3, 1, 3, 1, 3, '2024-11-21', '2027-10-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(8, 'SPV002', 'Rudi Hartono', NULL, NULL, '1984-02-07', 2, 4, 1, 2, 2, 4, '2021-11-21', '2028-06-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(9, 'STF001', 'Budi Santoso', NULL, NULL, '1996-11-23', 3, 5, 1, 1, 3, 1, '2022-11-21', '2028-02-21', 4, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(10, 'STF002', 'Dewi Sartika', NULL, NULL, '1994-06-21', 4, 5, 1, 1, 1, 2, '2024-11-21', '2028-01-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(11, 'STF003', 'Eko Prasetyo', NULL, NULL, '1991-07-28', 4, 6, 1, 4, 2, 3, '2024-11-21', '2027-03-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(12, 'STF004', 'Fitriani', NULL, NULL, '1982-04-14', 4, 7, 1, 3, 3, 4, '2025-11-21', '2027-12-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(13, 'STF005', 'Gunawan', NULL, NULL, '1986-01-04', 4, 8, 1, 2, 1, 1, '2025-11-21', '2027-04-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(14, 'ADM001', 'Hilda', NULL, NULL, '1987-02-15', 5, 6, 1, 5, 2, 2, '2023-11-21', '2027-10-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(15, 'OB001', 'Iwan', NULL, NULL, '1984-01-23', 10, 6, 1, 5, 3, 3, '2021-11-21', '2027-05-21', NULL, 'aktif', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(16, 'PROB001', 'Joko Probation', NULL, NULL, NULL, 5, NULL, NULL, 1, 1, 4, '2026-09-02', NULL, NULL, 'probation', NULL, 'TK/0', 0, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_klaim`
--

CREATE TABLE `hr_klaim` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `jenis_klaim_id` int(11) NOT NULL,
  `tanggal_klaim` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_klaim`
--

INSERT INTO `hr_klaim` (`id`, `karyawan_id`, `jenis_klaim_id`, `tanggal_klaim`, `jumlah`, `keterangan`, `bukti_file`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-11-11', '150000.00', 'Obat Flu', NULL, 'pending', NULL, NULL, '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(2, 2, 2, '2026-11-13', '75000.00', 'Bensin perjalanan dinas', NULL, 'approved', 1, '2026-11-21 20:42:15', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(3, 3, 1, '2026-11-16', '250000.00', 'Klaim tanpa struk', NULL, 'rejected', 1, '2026-11-21 20:42:15', '2026-11-21 20:42:15', '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_komponen_gaji`
--

CREATE TABLE `hr_komponen_gaji` (
  `id` int(11) NOT NULL,
  `nama_komponen` varchar(100) NOT NULL,
  `jenis` enum('pendapatan','potongan') NOT NULL,
  `tipe_hitung` enum('bulanan','harian','lembur') NOT NULL DEFAULT 'bulanan',
  `nilai_default` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Apakah komponen ini ditambahkan otomatis saat generate gaji',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_komponen_gaji`
--

INSERT INTO `hr_komponen_gaji` (`id`, `nama_komponen`, `jenis`, `tipe_hitung`, `nilai_default`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Tunjangan Transport', 'pendapatan', 'harian', '25000.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 'Tunjangan Makan', 'pendapatan', 'harian', '30000.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 'BPJS Kesehatan (1%)', 'potongan', 'bulanan', '100000.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(4, 'JHT (2%)', 'potongan', 'bulanan', '200000.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(5, 'Bonus Project', 'pendapatan', 'bulanan', '0.00', 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(6, 'Potongan Kasbon', 'potongan', 'bulanan', '0.00', 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(7, 'Upah Lembur', 'pendapatan', 'lembur', '0.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(8, 'Bonus KPI', 'pendapatan', 'bulanan', '1000000.00', 1, '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_kpi_indicators`
--

CREATE TABLE `hr_kpi_indicators` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `indikator` varchar(255) NOT NULL,
  `bobot` int(3) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_kpi_indicators`
--

INSERT INTO `hr_kpi_indicators` (`id`, `template_id`, `indikator`, `bobot`) VALUES
(1, 1, 'Kedisiplinan & Kehadiran', 20),
(2, 1, 'Kualitas Pekerjaan', 30),
(3, 1, 'Kerjasama Tim', 20),
(4, 1, 'Inisiatif', 15),
(5, 1, 'Pencapaian Target', 15);

-- --------------------------------------------------------

--
-- Table structure for table `hr_kpi_templates`
--

CREATE TABLE `hr_kpi_templates` (
  `id` int(11) NOT NULL,
  `nama_template` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_kpi_templates`
--

INSERT INTO `hr_kpi_templates` (`id`, `nama_template`, `keterangan`, `created_at`) VALUES
(1, 'Evaluasi Staff Umum', 'Template standar untuk staff', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_lembur`
--

CREATE TABLE `hr_lembur` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_lembur`
--

INSERT INTO `hr_lembur` (`id`, `karyawan_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `keterangan`, `status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-11-18', '17:00:00', '20:00:00', 'Menyelesaikan laporan bulanan', 'approved', NULL, NULL, '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(2, 1, '2026-11-21', '17:00:00', '19:00:00', 'Meeting dengan klien', 'pending', NULL, NULL, '2026-11-21 20:42:15', '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_libur_nasional`
--

CREATE TABLE `hr_libur_nasional` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `is_cuti_bersama` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_libur_nasional`
--

INSERT INTO `hr_libur_nasional` (`id`, `tanggal`, `keterangan`, `is_cuti_bersama`) VALUES
(1, '2026-01-01', 'Tahun Baru Masehi', 0),
(2, '2026-08-17', 'Hari Kemerdekaan RI', 0),
(3, '2026-12-25', 'Hari Raya Natal', 0);

-- --------------------------------------------------------

--
-- Table structure for table `hr_lowongan`
--

CREATE TABLE `hr_lowongan` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `jabatan_id` int(11) DEFAULT NULL,
  `divisi_id` int(11) DEFAULT NULL,
  `kuota` int(11) DEFAULT 1,
  `deskripsi` text DEFAULT NULL,
  `kualifikasi` text DEFAULT NULL,
  `status` enum('buka','tutup') DEFAULT 'buka',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_lowongan`
--

INSERT INTO `hr_lowongan` (`id`, `judul`, `jabatan_id`, `divisi_id`, `kuota`, `deskripsi`, `kualifikasi`, `status`, `tanggal_mulai`, `tanggal_selesai`, `created_at`) VALUES
(1, 'Staff IT Support', 4, 1, 2, 'Bertanggung jawab atas hardware dan software.', 'Min. D3 Informatika', 'buka', '2026-11-21', NULL, '2026-11-21 20:42:14'),
(2, 'Marketing Executive', 4, 4, 3, 'Mencari klien baru dan maintain klien lama.', 'Min. S1 Semua Jurusan, Komunikatif', 'buka', '2026-11-21', NULL, '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_offboarding`
--

CREATE TABLE `hr_offboarding` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tipe` enum('resign','terminate') NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `tanggal_efektif` date NOT NULL COMMENT 'Last working day',
  `alasan` text DEFAULT NULL,
  `checklist_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist_data`)),
  `status` enum('proses','selesai') NOT NULL DEFAULT 'proses',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_offboarding`
--

INSERT INTO `hr_offboarding` (`id`, `karyawan_id`, `tipe`, `tanggal_pengajuan`, `tanggal_efektif`, `alasan`, `checklist_data`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, 'resign', '2026-11-06', '2026-12-06', 'Mendapat tawaran di perusahaan lain.', NULL, 'proses', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 6, 'terminate', '2026-10-07', '2026-11-06', 'Pelanggaran kontrak berat.', NULL, 'selesai', '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_pelamar`
--

CREATE TABLE `hr_pelamar` (
  `id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `file_cv` varchar(255) DEFAULT NULL,
  `file_spk_signed` varchar(255) DEFAULT NULL,
  `status` enum('applied','screening','interview','offering','hired','rejected') DEFAULT 'applied',
  `catatan` text DEFAULT NULL,
  `tanggal_lamar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_pelamar`
--

INSERT INTO `hr_pelamar` (`id`, `lowongan_id`, `nama_lengkap`, `email`, `no_hp`, `pendidikan_terakhir`, `file_cv`, `file_spk_signed`, `status`, `catatan`, `tanggal_lamar`) VALUES
(1, 1, 'Doni Pratama', 'doni@example.com', '081234567890', 'S1 Teknik Informatika', NULL, NULL, 'interview', 'Kandidat potensial, skill teknis bagus.', '2026-11-21 20:42:14'),
(2, 1, 'Eka Saputra', 'eka@example.com', '089876543210', 'D3 Manajemen Informatika', NULL, NULL, 'applied', 'Baru lulus.', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_pengajuan_cuti`
--

CREATE TABLE `hr_pengajuan_cuti` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `jenis_cuti_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jumlah_hari` int(3) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `lampiran_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` text DEFAULT NULL COMMENT 'JSON array of user IDs who approved',
  `next_approver_id` int(11) DEFAULT NULL COMMENT 'User ID of the next person in the approval chain',
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_pengajuan_cuti`
--

INSERT INTO `hr_pengajuan_cuti` (`id`, `karyawan_id`, `jenis_cuti_id`, `tanggal_mulai`, `tanggal_selesai`, `jumlah_hari`, `keterangan`, `lampiran_file`, `status`, `approved_by`, `next_approver_id`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-11-16', '2026-11-18', 3, 'Liburan keluarga', NULL, 'approved', NULL, NULL, '2026-11-21 20:42:14', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(2, 2, 2, '2026-11-19', '2026-11-19', 1, 'Demam, surat dokter terlampir', NULL, 'approved', NULL, NULL, '2026-11-21 20:42:14', '2026-11-21 20:42:14', '2026-11-21 20:42:14'),
(3, 3, 1, '2026-12-01', '2026-12-03', 3, 'Acara keluarga di luar kota', NULL, 'pending', NULL, NULL, NULL, '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_pengaturan_pajak`
--

CREATE TABLE `hr_pengaturan_pajak` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_pengaturan_pajak`
--

INSERT INTO `hr_pengaturan_pajak` (`setting_key`, `setting_value`) VALUES
('bpjs_kes_karyawan', '1.0'),
('bpjs_kes_perusahaan', '4.0'),
('bpjs_tk_jht_karyawan', '2.0'),
('bpjs_tk_jht_perusahaan', '3.7'),
('bpjs_tk_jp_karyawan', '1.0'),
('bpjs_tk_jp_perusahaan', '2.0'),
('ptkp_k0', '58500000'),
('ptkp_k1', '63000000'),
('ptkp_k2', '67500000'),
('ptkp_k3', '72000000'),
('ptkp_tk0', '54000000');

-- --------------------------------------------------------

--
-- Table structure for table `hr_penggajian`
--

CREATE TABLE `hr_penggajian` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `periode_bulan` int(2) NOT NULL,
  `periode_tahun` int(4) NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tunjangan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_gaji` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','final') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_penggajian`
--

INSERT INTO `hr_penggajian` (`id`, `karyawan_id`, `periode_bulan`, `periode_tahun`, `gaji_pokok`, `tunjangan`, `potongan`, `total_gaji`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 2026, '12000000.00', '11000000.00', '300000.00', '22700000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(2, 2, 10, 2026, '8000000.00', '9000000.00', '300000.00', '16700000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(3, 3, 10, 2026, '6000000.00', '8000000.00', '300000.00', '13700000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(4, 4, 10, 2026, '4500000.00', '8000000.00', '300000.00', '12200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(5, 5, 10, 2026, '12000000.00', '3500000.00', '300000.00', '15200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(6, 6, 10, 2026, '8000000.00', '3500000.00', '300000.00', '11200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(7, 7, 10, 2026, '6000000.00', '2500000.00', '300000.00', '8200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(8, 8, 10, 2026, '4500000.00', '2500000.00', '300000.00', '6700000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(9, 9, 10, 2026, '12000000.00', '1800000.00', '300000.00', '13500000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(10, 10, 10, 2026, '8000000.00', '1500000.00', '300000.00', '9200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(11, 11, 10, 2026, '6000000.00', '1500000.00', '300000.00', '7200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(12, 12, 10, 2026, '4500000.00', '1500000.00', '300000.00', '5700000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(13, 13, 10, 2026, '12000000.00', '1500000.00', '300000.00', '13200000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(14, 14, 10, 2026, '8000000.00', '1300000.00', '300000.00', '9000000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(15, 15, 10, 2026, '6000000.00', '1150000.00', '300000.00', '6850000.00', 'final', '2026-11-21 20:42:15', '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_penggajian_komponen`
--

CREATE TABLE `hr_penggajian_komponen` (
  `id` int(11) NOT NULL,
  `penggajian_id` int(11) NOT NULL,
  `komponen_id` int(11) NOT NULL,
  `nama_komponen` varchar(100) NOT NULL COMMENT 'Denormalized name for easier reporting',
  `jenis` enum('pendapatan','potongan') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_penggajian_komponen`
--

INSERT INTO `hr_penggajian_komponen` (`id`, `penggajian_id`, `komponen_id`, `nama_komponen`, `jenis`, `jumlah`) VALUES
(1, 1, 0, 'Tunjangan Jabatan', 'pendapatan', '10000000.00'),
(2, 1, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(3, 1, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(4, 1, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(5, 1, 4, 'JHT (2%)', 'potongan', '200000.00'),
(6, 1, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(7, 1, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(8, 2, 0, 'Tunjangan Jabatan', 'pendapatan', '8000000.00'),
(9, 2, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(10, 2, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(11, 2, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(12, 2, 4, 'JHT (2%)', 'potongan', '200000.00'),
(13, 2, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(14, 2, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(15, 3, 0, 'Tunjangan Jabatan', 'pendapatan', '7000000.00'),
(16, 3, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(17, 3, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(18, 3, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(19, 3, 4, 'JHT (2%)', 'potongan', '200000.00'),
(20, 3, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(21, 3, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(22, 4, 0, 'Tunjangan Jabatan', 'pendapatan', '7000000.00'),
(23, 4, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(24, 4, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(25, 4, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(26, 4, 4, 'JHT (2%)', 'potongan', '200000.00'),
(27, 4, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(28, 4, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(29, 5, 0, 'Tunjangan Jabatan', 'pendapatan', '2500000.00'),
(30, 5, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(31, 5, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(32, 5, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(33, 5, 4, 'JHT (2%)', 'potongan', '200000.00'),
(34, 5, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(35, 5, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(36, 6, 0, 'Tunjangan Jabatan', 'pendapatan', '2500000.00'),
(37, 6, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(38, 6, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(39, 6, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(40, 6, 4, 'JHT (2%)', 'potongan', '200000.00'),
(41, 6, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(42, 6, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(43, 7, 0, 'Tunjangan Jabatan', 'pendapatan', '1500000.00'),
(44, 7, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(45, 7, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(46, 7, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(47, 7, 4, 'JHT (2%)', 'potongan', '200000.00'),
(48, 7, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(49, 7, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(50, 8, 0, 'Tunjangan Jabatan', 'pendapatan', '1500000.00'),
(51, 8, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(52, 8, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(53, 8, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(54, 8, 4, 'JHT (2%)', 'potongan', '200000.00'),
(55, 8, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(56, 8, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(57, 9, 0, 'Tunjangan Jabatan', 'pendapatan', '800000.00'),
(58, 9, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(59, 9, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(60, 9, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(61, 9, 4, 'JHT (2%)', 'potongan', '200000.00'),
(62, 9, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(63, 9, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(64, 10, 0, 'Tunjangan Jabatan', 'pendapatan', '500000.00'),
(65, 10, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(66, 10, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(67, 10, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(68, 10, 4, 'JHT (2%)', 'potongan', '200000.00'),
(69, 10, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(70, 10, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(71, 11, 0, 'Tunjangan Jabatan', 'pendapatan', '500000.00'),
(72, 11, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(73, 11, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(74, 11, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(75, 11, 4, 'JHT (2%)', 'potongan', '200000.00'),
(76, 11, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(77, 11, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(78, 12, 0, 'Tunjangan Jabatan', 'pendapatan', '500000.00'),
(79, 12, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(80, 12, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(81, 12, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(82, 12, 4, 'JHT (2%)', 'potongan', '200000.00'),
(83, 12, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(84, 12, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(85, 13, 0, 'Tunjangan Jabatan', 'pendapatan', '500000.00'),
(86, 13, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(87, 13, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(88, 13, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(89, 13, 4, 'JHT (2%)', 'potongan', '200000.00'),
(90, 13, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(91, 13, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(92, 14, 0, 'Tunjangan Jabatan', 'pendapatan', '300000.00'),
(93, 14, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(94, 14, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(95, 14, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(96, 14, 4, 'JHT (2%)', 'potongan', '200000.00'),
(97, 14, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(98, 14, 8, 'Bonus KPI', 'pendapatan', '1000000.00'),
(99, 15, 0, 'Tunjangan Jabatan', 'pendapatan', '150000.00'),
(100, 15, 1, 'Tunjangan Transport', 'pendapatan', '0.00'),
(101, 15, 2, 'Tunjangan Makan', 'pendapatan', '0.00'),
(102, 15, 3, 'BPJS Kesehatan (1%)', 'potongan', '100000.00'),
(103, 15, 4, 'JHT (2%)', 'potongan', '200000.00'),
(104, 15, 7, 'Upah Lembur', 'pendapatan', '0.00'),
(105, 15, 8, 'Bonus KPI', 'pendapatan', '1000000.00');

-- --------------------------------------------------------

--
-- Table structure for table `hr_pengumuman`
--

CREATE TABLE `hr_pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `lampiran_file` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_pengumuman`
--

INSERT INTO `hr_pengumuman` (`id`, `judul`, `isi`, `lampiran_file`, `created_by`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 'Libur Cuti Bersama Idul Fitri', 'Diberitahukan kepada seluruh karyawan bahwa akan ada libur cuti bersama dalam rangka Hari Raya Idul Fitri. Detail jadwal akan diinformasikan lebih lanjut.', NULL, 1, 1, '2026-11-20 20:42:14', '2026-11-21 20:42:14'),
(2, 'Update Kebijakan Work From Home', 'Akan ada pembaruan kebijakan WFH yang akan diumumkan minggu depan.', NULL, 1, 0, '2026-11-21 20:42:14', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_penilaian_detail`
--

CREATE TABLE `hr_penilaian_detail` (
  `id` int(11) NOT NULL,
  `penilaian_id` int(11) NOT NULL,
  `indikator_id` int(11) NOT NULL,
  `skor` int(3) NOT NULL,
  `komentar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_penilaian_detail`
--

INSERT INTO `hr_penilaian_detail` (`id`, `penilaian_id`, `indikator_id`, `skor`, `komentar`) VALUES
(1, 1, 1, 85, NULL),
(2, 1, 2, 90, NULL),
(3, 1, 3, 80, NULL),
(4, 1, 4, 75, NULL),
(5, 1, 5, 95, NULL),
(6, 2, 1, 90, NULL),
(7, 2, 2, 85, NULL),
(8, 2, 3, 88, NULL),
(9, 2, 4, 92, NULL),
(10, 2, 5, 80, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hr_penilaian_kinerja`
--

CREATE TABLE `hr_penilaian_kinerja` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `periode_bulan` int(2) NOT NULL,
  `periode_tahun` int(4) NOT NULL,
  `tanggal_penilaian` date NOT NULL,
  `penilai_id` int(11) NOT NULL,
  `total_skor` decimal(5,2) DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `status` enum('draft','final') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_penilaian_kinerja`
--

INSERT INTO `hr_penilaian_kinerja` (`id`, `karyawan_id`, `template_id`, `periode_bulan`, `periode_tahun`, `tanggal_penilaian`, `penilai_id`, `total_skor`, `catatan`, `status`, `created_at`) VALUES
(1, 9, 1, 10, 2026, '2026-10-28', 1, '85.50', 'Kinerja Budi sangat baik bulan ini.', 'final', '2026-11-21 20:42:15'),
(2, 2, 1, 10, 2026, '2026-10-28', 1, '86.90', 'Siti menunjukkan peningkatan yang signifikan.', 'final', '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_request_surat`
--

CREATE TABLE `hr_request_surat` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `jenis_surat` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('pending','processed','completed','rejected') DEFAULT 'pending',
  `file_path` varchar(255) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_request_surat`
--

INSERT INTO `hr_request_surat` (`id`, `karyawan_id`, `jenis_surat`, `keterangan`, `status`, `file_path`, `admin_note`, `created_at`, `updated_at`) VALUES
(1, 9, 'paklaring', 'Untuk pengajuan KPR', 'pending', NULL, NULL, '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(2, 2, 'keterangan_kerja', 'Syarat pembuatan Visa', 'completed', NULL, 'Surat telah dikirim via email', '2026-11-21 20:42:15', '2026-11-21 20:42:15'),
(3, 3, 'keterangan_penghasilan', 'Pengajuan kartu kredit', 'rejected', NULL, 'Data gaji belum final', '2026-11-21 20:42:15', '2026-11-21 20:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `hr_riwayat_gaji`
--

CREATE TABLE `hr_riwayat_gaji` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `tanggal_perubahan` date NOT NULL,
  `gaji_lama` decimal(15,2) NOT NULL,
  `gaji_baru` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_riwayat_gaji`
--

INSERT INTO `hr_riwayat_gaji` (`id`, `karyawan_id`, `tanggal_perubahan`, `gaji_lama`, `gaji_baru`, `keterangan`, `created_at`) VALUES
(1, 7, '2026-08-21', '7500000.00', '8000000.00', 'Penyesuaian Kinerja', '2026-11-21 20:42:14'),
(2, 9, '2026-05-21', '4500000.00', '6000000.00', 'Promosi Jabatan', '2026-11-21 20:42:14'),
(3, 9, '2025-11-21', '4000000.00', '4500000.00', 'Kenaikan Berkala', '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `hr_riwayat_jabatan`
--

CREATE TABLE `hr_riwayat_jabatan` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `jabatan_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_riwayat_jabatan`
--

INSERT INTO `hr_riwayat_jabatan` (`id`, `karyawan_id`, `jabatan_id`, `tanggal_mulai`, `tanggal_selesai`, `created_at`) VALUES
(1, 1, 6, '2023-11-21', NULL, '2026-11-21 20:36:33'),
(2, 2, 7, '2021-11-21', NULL, '2026-11-21 20:36:33'),
(3, 3, 8, '2022-11-21', NULL, '2026-11-21 20:36:33'),
(4, 4, 9, '2022-11-21', NULL, '2026-11-21 20:36:33'),
(5, 5, 1, '2024-11-21', NULL, '2026-11-21 20:36:33'),
(6, 6, 1, '2021-11-21', NULL, '2026-11-21 20:36:33'),
(7, 7, 2, '2025-11-21', NULL, '2026-11-21 20:36:33'),
(8, 8, 2, '2021-11-21', NULL, '2026-11-21 20:36:33'),
(9, 9, 3, '2022-11-21', NULL, '2026-11-21 20:36:33'),
(10, 9, 4, '2024-11-21', '2026-05-21', '2026-11-21 20:36:33'),
(11, 10, 4, '2025-11-21', NULL, '2026-11-21 20:36:33'),
(12, 11, 4, '2021-11-21', NULL, '2026-11-21 20:36:33'),
(13, 12, 4, '2022-11-21', NULL, '2026-11-21 20:36:33'),
(14, 13, 4, '2024-11-21', NULL, '2026-11-21 20:36:33'),
(15, 14, 5, '2025-11-21', NULL, '2026-11-21 20:36:33'),
(16, 15, 10, '2023-11-21', NULL, '2026-11-21 20:36:33'),
(17, 1, 6, '2023-11-21', NULL, '2026-11-21 20:42:14'),
(18, 2, 7, '2025-11-21', NULL, '2026-11-21 20:42:14'),
(19, 3, 8, '2022-11-21', NULL, '2026-11-21 20:42:14'),
(20, 4, 9, '2022-11-21', NULL, '2026-11-21 20:42:14'),
(21, 5, 1, '2021-11-21', NULL, '2026-11-21 20:42:14'),
(22, 6, 1, '2023-11-21', NULL, '2026-11-21 20:42:14'),
(23, 7, 2, '2024-11-21', NULL, '2026-11-21 20:42:14'),
(24, 8, 2, '2021-11-21', NULL, '2026-11-21 20:42:14'),
(25, 9, 3, '2022-11-21', NULL, '2026-11-21 20:42:14'),
(26, 9, 4, '2024-11-21', '2026-05-21', '2026-11-21 20:42:14'),
(27, 10, 4, '2024-11-21', NULL, '2026-11-21 20:42:14'),
(28, 11, 4, '2024-11-21', NULL, '2026-11-21 20:42:14'),
(29, 12, 4, '2025-11-21', NULL, '2026-11-21 20:42:14'),
(30, 13, 4, '2025-11-21', NULL, '2026-11-21 20:42:14'),
(31, 14, 5, '2023-11-21', NULL, '2026-11-21 20:42:14'),
(32, 15, 10, '2021-11-21', NULL, '2026-11-21 20:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `inventory_account_id` int(11) DEFAULT NULL COMMENT 'Akun Persediaan (Aset)',
  `cogs_account_id` int(11) DEFAULT NULL COMMENT 'Akun HPP (Beban)',
  `revenue_account_id` int(11) DEFAULT NULL COMMENT 'Akun Pendapatan Penjualan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_categories`
--

CREATE TABLE `item_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_details`
--

CREATE TABLE `jurnal_details` (
  `id` int(11) NOT NULL,
  `jurnal_entry_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kredit` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal_details`
--

INSERT INTO `jurnal_details` (`id`, `jurnal_entry_id`, `account_id`, `debit`, `kredit`) VALUES
(1, 101, 105, '5000000.00', '0.00'),
(2, 101, 202, '0.00', '5000000.00'),
(3, 102, 500, '4500000.00', '0.00'),
(4, 102, 105, '0.00', '4500000.00'),
(5, 103, 500, '4920000.00', '0.00'),
(6, 103, 105, '0.00', '4920000.00'),
(7, 104, 105, '10000000.00', '0.00'),
(8, 104, 202, '0.00', '10000000.00'),
(9, 105, 500, '5700000.00', '0.00'),
(10, 105, 105, '0.00', '5700000.00'),
(11, 106, 500, '15000000.00', '0.00'),
(12, 106, 105, '0.00', '15000000.00'),
(13, 107, 500, '6600000.00', '0.00'),
(14, 107, 105, '0.00', '6600000.00'),
(15, 108, 107, '7000000.00', '0.00'),
(16, 108, 104, '0.00', '7000000.00');

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_entries`
--

CREATE TABLE `jurnal_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `status` enum('aktif','dibatalkan') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal_entries`
--

INSERT INTO `jurnal_entries` (`id`, `user_id`, `tanggal`, `keterangan`, `created_by`, `updated_at`, `updated_by`, `status`, `created_at`) VALUES
(101, 1, '2026-01-10', 'Pembelian barang dagang (buku tulis) dari Supplier A secara kredit', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(102, 1, '2026-01-15', 'Pencatatan HPP atas penjualan tunai Januari', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(103, 1, '2026-02-20', 'Pencatatan HPP atas penjualan tunai Februari', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(104, 1, '2026-04-05', 'Pembelian barang dagang (seragam) dari Supplier B secara kredit', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(105, 1, '2026-04-18', 'Pencatatan HPP atas penjualan tunai April', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(106, 1, '2026-07-15', 'Pencatatan HPP atas penjualan tahun ajaran baru', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(107, 1, '2026-08-10', 'Pencatatan HPP atas penjualan tunai Agustus', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29'),
(108, 1, '2026-03-01', 'Pembelian Komputer Baru untuk Kantor', NULL, '2026-11-21 13:36:29', NULL, 'aktif', '2026-11-21 13:36:29');

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stok`
--

CREATE TABLE `kartu_stok` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `debit` int(11) NOT NULL DEFAULT 0,
  `kredit` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL COMMENT 'ID referensi transaksi (misal: id penjualan)',
  `source` varchar(50) DEFAULT NULL COMMENT 'Sumber transaksi (misal: penjualan, pembelian)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'FK ke tabel users, pemilik data',
  `supplier_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel suppliers, pemasok barang/jasa',
  `nomor_referensi` varchar(50) DEFAULT NULL COMMENT 'Nomor unik untuk pembelian, bisa otomatis atau manual',
  `tanggal_pembelian` date NOT NULL COMMENT 'Tanggal terjadinya pembelian',
  `jatuh_tempo` date DEFAULT NULL COMMENT 'Tanggal jatuh tempo pembayaran (jika kredit)',
  `total` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total nilai pembelian',
  `keterangan` text DEFAULT NULL COMMENT 'Deskripsi umum pembelian',
  `status` enum('draft','open','paid','void') NOT NULL DEFAULT 'open' COMMENT 'Status pembelian: open (utang), paid (lunas)',
  `payment_method` enum('credit','cash') NOT NULL DEFAULT 'credit' COMMENT 'Metode pembayaran: kredit (utang) atau cash (tunai)',
  `credit_account_id` int(11) NOT NULL COMMENT 'Akun yang dikredit (Utang Usaha jika kredit, atau Akun Kas/Bank jika tunai)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users, siapa yang membuat',
  `updated_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users, siapa yang mengubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_details`
--

CREATE TABLE `pembelian_details` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) NOT NULL COMMENT 'FK ke tabel pembelian',
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `account_id` int(11) NOT NULL COMMENT 'Akun yang didebit (Beban atau Aset)',
  `deskripsi` varchar(255) DEFAULT NULL COMMENT 'Deskripsi tidak lagi dipakai, tapi dipertahankan untuk data lama',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal (qty * price)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'FK ke tabel users, pemilik data',
  `customer_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel customers (opsional)',
  `nomor_referensi` varchar(50) NOT NULL COMMENT 'Nomor unik untuk penjualan, misal: INV/20240101/0001',
  `tanggal_penjualan` datetime NOT NULL COMMENT 'Tanggal dan waktu terjadinya penjualan',
  `customer_name` varchar(255) DEFAULT 'Umum',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total sebelum diskon',
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total diskon keseluruhan',
  `total` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total nilai penjualan',
  `bayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jumlah uang yang dibayarkan customer',
  `kembali` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jumlah uang kembalian',
  `keterangan` text DEFAULT NULL COMMENT 'Catatan atau deskripsi umum penjualan',
  `status` enum('completed','void') NOT NULL DEFAULT 'completed' COMMENT 'Status transaksi: completed, void (dibatalkan)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users, siapa yang membuat',
  `updated_by` int(11) DEFAULT NULL COMMENT 'FK ke tabel users, siapa yang mengubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_details`
--

CREATE TABLE `penjualan_details` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL COMMENT 'FK ke tabel penjualan',
  `item_id` int(11) NOT NULL COMMENT 'FK ke tabel items',
  `deskripsi_item` varchar(255) NOT NULL COMMENT 'Nama/deskripsi item saat transaksi',
  `quantity` int(11) NOT NULL COMMENT 'Jumlah item yang dijual',
  `price` decimal(15,2) NOT NULL COMMENT 'Harga jual satuan saat transaksi',
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Diskon per item',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'Subtotal (quantity * price)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `slug`, `name`, `description`) VALUES
(1, 'transaksi.create', 'Membuat Transaksi', NULL),
(2, 'transaksi.view', 'Melihat Riwayat Transaksi', NULL),
(3, 'laporan.view', 'Melihat Laporan Keuangan', NULL),
(4, 'settings.manage', 'Mengelola Pengaturan Aplikasi', NULL),
(5, 'users.manage', 'Mengelola Pengguna', NULL),
(6, 'menu.view.transaksi', 'Lihat Menu: Transaksi', 'Memberi akses untuk melihat grup menu Transaksi'),
(7, 'menu.view.akuntansi', 'Lihat Menu: Akuntansi', 'Memberi akses untuk melihat grup menu Akuntansi'),
(8, 'menu.view.stok', 'Lihat Menu: Stok & Inventaris', 'Memberi akses untuk melihat grup menu Stok & Inventaris'),
(9, 'menu.view.laporan', 'Lihat Menu: Laporan', 'Memberi akses untuk melihat grup menu Laporan'),
(10, 'menu.view.tools', 'Lihat Menu: Alat & Proses', 'Memberi akses untuk melihat grup menu Alat & Proses'),
(11, 'menu.view.administrasi', 'Lihat Menu: Administrasi', 'Memberi akses untuk melihat grup menu Administrasi'),
(12, 'menu.view.users', 'Lihat Sub-Menu: Users', 'Memberi akses untuk melihat sub-menu Users'),
(13, 'menu.view.roles', 'Lihat Sub-Menu: Roles', 'Memberi akses untuk melihat sub-menu Manajemen Role'),
(14, 'menu.view.activity-log', 'Lihat Sub-Menu: Log Aktivitas', 'Memberi akses untuk melihat sub-menu Log Aktivitas'),
(15, 'menu.view.tutup-buku', 'Lihat Sub-Menu: Tutup Buku', 'Memberi akses untuk melihat sub-menu Tutup Buku'),
(16, 'menu.view.settings', 'Lihat Sub-Menu: Pengaturan', 'Memberi akses untuk melihat sub-menu Pengaturan');

-- --------------------------------------------------------

--
-- Table structure for table `reconciliations`
--

CREATE TABLE `reconciliations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `statement_date` date NOT NULL COMMENT 'Tanggal akhir periode rekonsiliasi',
  `statement_balance` decimal(15,2) NOT NULL COMMENT 'Saldo akhir dari rekening koran',
  `cleared_balance` decimal(15,2) NOT NULL COMMENT 'Saldo buku setelah transaksi yang cocok dibersihkan',
  `difference` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurring_templates`
--

CREATE TABLE `recurring_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL COMMENT 'Nama template, cth: "Sewa Kantor Bulanan"',
  `frequency_unit` enum('day','week','month','year') NOT NULL,
  `frequency_interval` int(11) NOT NULL DEFAULT 1 COMMENT 'cth: 1 bulan, 2 minggu',
  `start_date` date NOT NULL,
  `next_run_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `template_type` enum('transaksi','jurnal') NOT NULL,
  `template_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Data JSON dari transaksi/jurnal yang akan dibuat' CHECK (json_valid(`template_data`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recurring_templates`
--

INSERT INTO `recurring_templates` (`id`, `user_id`, `name`, `frequency_unit`, `frequency_interval`, `start_date`, `next_run_date`, `end_date`, `template_type`, `template_data`, `is_active`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 'Beban Sewa Toko Bulanan', 'month', 1, '2026-01-25', '2026-01-25', NULL, 'jurnal', '{\"keterangan\": \"Pembayaran sewa toko bulanan\", \"lines\": [{\"account_id\": \"605\", \"debit\": 500000, \"kredit\": 0}, {\"account_id\": \"104\", \"debit\": 0, \"kredit\": 500000}]}', 1, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin', 'Administrator dengan akses penuh'),
(2, 'Kasir', 'Staff yang menangani transaksi harian'),
(3, 'Manager', 'Melihat laporan dan analisis'),
(4, 'Karyawan', 'Akses untuk portal karyawan self-service');

-- --------------------------------------------------------

--
-- Table structure for table `role_menus`
--

CREATE TABLE `role_menus` (
  `role_id` int(11) NOT NULL,
  `menu_key` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_menus`
--

INSERT INTO `role_menus` (`role_id`, `menu_key`) VALUES
(4, 'portal_absensi'),
(4, 'portal_dashboard'),
(4, 'portal_directory'),
(4, 'portal_dokumen'),
(4, 'portal_kalender'),
(4, 'portal_karyawan'),
(4, 'portal_klaim'),
(4, 'portal_kpi'),
(4, 'portal_lembur'),
(4, 'portal_pengajuan_cuti'),
(4, 'portal_profil'),
(4, 'portal_slip_gaji');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(2, 1),
(2, 2),
(2, 6),
(3, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('app_city', 'Ternate'),
('app_logo', 'uploads/settings/app_logo_6b01b6839e2b9.png'),
('app_name', 'HRIS - Portal'),
('consignment_cash_account', '103'),
('consignment_cogs_account', '501'),
('consignment_payable_account', '203'),
('consignment_revenue_account', '402'),
('login_bg_color', '#075e54'),
('login_btn_color', '#25d366'),
('notification_interval', '60000'),
('pdf_header_line1', 'Dewi'),
('pdf_header_line2', 'Muara Group'),
('pdf_header_line3', 'Jl. Merdeka, Gamalama, Kec. Ternate Tengah, Kota Ternate, Maluku Utara 97711'),
('period_lock_date', '2025-12-31'),
('retained_earnings_account_id', '303'),
('signature_bendahara_name', 'Hasna'),
('signature_ketua_name', 'Asti');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `stok_sebelum` int(11) NOT NULL,
  `stok_setelah` int(11) NOT NULL,
  `selisih_kuantitas` int(11) NOT NULL,
  `selisih_nilai` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pemasok` varchar(100) NOT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `user_id`, `nama_pemasok`, `kontak`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 'Penerbit Erlangga', '021-8717006', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL),
(2, 1, 'CV. Seragam Jaya', '08123456789', '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL COMMENT 'Akun yang didebit/dikredit (Pendapatan/Beban/Utang)',
  `tanggal` date NOT NULL,
  `jenis` enum('pemasukan','pengeluaran','transfer') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `nomor_referensi` varchar(50) DEFAULT NULL COMMENT 'Nomor faktur/transaksi',
  `kas_account_id` int(11) NOT NULL COMMENT 'Akun kas/bank yang terpengaruh',
  `kas_tujuan_account_id` int(11) DEFAULT NULL COMMENT 'Untuk transfer antar akun kas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `status` enum('aktif','dibatalkan') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `account_id`, `tanggal`, `jenis`, `jumlah`, `keterangan`, `nomor_referensi`, `kas_account_id`, `kas_tujuan_account_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) VALUES
(1, 1, 401, '2026-01-15', 'pemasukan', '7500000.00', 'Penjualan tunai ATK dan seragam', 'INV/2024/01/001', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(2, 1, 602, '2026-01-28', 'pengeluaran', '250000.00', 'Pembayaran listrik dan air Januari', 'BILL/2024/01/01', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(3, 1, 202, '2026-02-10', 'pengeluaran', '2000000.00', 'Pembayaran sebagian utang ke Supplier Buku', 'PAY/2024/02/01', 104, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(4, 1, 401, '2026-02-20', 'pemasukan', '8200000.00', 'Penjualan tunai Februari', 'INV/2024/02/001', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(5, 1, 601, '2026-03-25', 'pengeluaran', '1500000.00', 'Gaji karyawan toko Maret', 'PAY/2024/03/02', 104, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(6, 1, 401, '2026-04-18', 'pemasukan', '9500000.00', 'Penjualan tunai April', 'INV/2024/04/001', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(7, 1, 603, '2026-05-05', 'pengeluaran', '150000.00', 'Pembelian perlengkapan toko (kantong plastik, dll)', 'EXP/2024/05/01', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(8, 1, 601, '2026-06-25', 'pengeluaran', '1500000.00', 'Gaji karyawan toko Juni', 'PAY/2024/06/01', 104, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(9, 1, 401, '2026-07-15', 'pemasukan', '25000000.00', 'Penjualan buku dan seragam tahun ajaran baru', 'INV/2024/07/001', 104, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(10, 1, 401, '2026-08-10', 'pemasukan', '11000000.00', 'Penjualan tunai Agustus', 'INV/2024/08/001', 103, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif'),
(11, 1, 601, '2026-09-25', 'pengeluaran', '1500000.00', 'Gaji karyawan toko September', 'PAY/2024/09/01', 104, NULL, '2026-11-21 13:36:29', NULL, '2026-11-21 13:36:29', NULL, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `nama_lengkap`, `foto_profil`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$d3KIyN81RJM9DKlx8DrCqeCPT8D8e6N1kh/l2fZ/Q/t0o/HsrDgB6', 1, 'Administrator', NULL, 'admin', 1, '2026-11-21 13:36:29'),
(2, 'user', '$2y$10$1BrINmP/CgCaH4vLmWdIselAyCT7sXf6lycP5LbxSEG5KNEC9hbhe', 4, 'User Biasa', NULL, 'user', 1, '2026-11-21 13:36:29'),
(4, 'budi', '$2y$10$l1wDF1tqXaPYlXkGH7kLuu5OvLaIZKmKYGvX0ob.xlW7ykDVVeDkG', 4, 'Budi Santoso', NULL, 'user', 1, '2026-11-21 13:42:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_kode_akun` (`user_id`,`kode_akun`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_account_periode` (`user_id`,`account_id`,`periode_tahun`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `consignment_items`
--
ALTER TABLE `consignment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `akun_aset_id` (`akun_aset_id`),
  ADD KEY `akun_akumulasi_penyusutan_id` (`akun_akumulasi_penyusutan_id`),
  ADD KEY `akun_beban_penyusutan_id` (`akun_beban_penyusutan_id`);

--
-- Indexes for table `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `ref_id_type` (`ref_id`,`ref_type`),
  ADD KEY `tanggal` (`tanggal`),
  ADD KEY `consignment_item_id` (`consignment_item_id`),
  ADD KEY `idx_reconciliation_id` (`reconciliation_id`),
  ADD KEY `idx_reconciliation` (`account_id`,`is_reconciled`,`tanggal`);

--
-- Indexes for table `hr_absensi`
--
ALTER TABLE `hr_absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_absensi_golongan`
--
ALTER TABLE `hr_absensi_golongan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_golongan` (`nama_golongan`);

--
-- Indexes for table `hr_absensi_status`
--
ALTER TABLE `hr_absensi_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_status` (`nama_status`);

--
-- Indexes for table `hr_divisi`
--
ALTER TABLE `hr_divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_dokumen_karyawan`
--
ALTER TABLE `hr_dokumen_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_dokumen_perusahaan`
--
ALTER TABLE `hr_dokumen_perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_evaluasi_probation`
--
ALTER TABLE `hr_evaluasi_probation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_golongan_gaji`
--
ALTER TABLE `hr_golongan_gaji`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_jabatan`
--
ALTER TABLE `hr_jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_jadwal_kerja`
--
ALTER TABLE `hr_jadwal_kerja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_jadwal` (`nama_jadwal`);

--
-- Indexes for table `hr_jatah_cuti`
--
ALTER TABLE `hr_jatah_cuti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `karyawan_tahun` (`karyawan_id`,`tahun`);

--
-- Indexes for table `hr_jenis_cuti`
--
ALTER TABLE `hr_jenis_cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_jenis_klaim`
--
ALTER TABLE `hr_jenis_klaim`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_kantor`
--
ALTER TABLE `hr_kantor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_karyawan`
--
ALTER TABLE `hr_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `jadwal_kerja_id` (`jadwal_kerja_id`),
  ADD KEY `jabatan_id` (`jabatan_id`),
  ADD KEY `atasan_id` (`atasan_id`),
  ADD KEY `divisi_id` (`divisi_id`),
  ADD KEY `kantor_id` (`kantor_id`),
  ADD KEY `golongan_gaji_id` (`golongan_gaji_id`);

--
-- Indexes for table `hr_klaim`
--
ALTER TABLE `hr_klaim`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`),
  ADD KEY `jenis_klaim_id` (`jenis_klaim_id`);

--
-- Indexes for table `hr_komponen_gaji`
--
ALTER TABLE `hr_komponen_gaji`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_kpi_indicators`
--
ALTER TABLE `hr_kpi_indicators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `hr_kpi_templates`
--
ALTER TABLE `hr_kpi_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_lembur`
--
ALTER TABLE `hr_lembur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_libur_nasional`
--
ALTER TABLE `hr_libur_nasional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indexes for table `hr_lowongan`
--
ALTER TABLE `hr_lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_offboarding`
--
ALTER TABLE `hr_offboarding`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_pelamar`
--
ALTER TABLE `hr_pelamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lowongan_id` (`lowongan_id`);

--
-- Indexes for table `hr_pengajuan_cuti`
--
ALTER TABLE `hr_pengajuan_cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_pengaturan_pajak`
--
ALTER TABLE `hr_pengaturan_pajak`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `hr_penggajian`
--
ALTER TABLE `hr_penggajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_penggajian_komponen`
--
ALTER TABLE `hr_penggajian_komponen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penggajian_id` (`penggajian_id`),
  ADD KEY `komponen_id` (`komponen_id`);

--
-- Indexes for table `hr_pengumuman`
--
ALTER TABLE `hr_pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_penilaian_detail`
--
ALTER TABLE `hr_penilaian_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penilaian_id` (`penilaian_id`);

--
-- Indexes for table `hr_penilaian_kinerja`
--
ALTER TABLE `hr_penilaian_kinerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `hr_request_surat`
--
ALTER TABLE `hr_request_surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_riwayat_gaji`
--
ALTER TABLE `hr_riwayat_gaji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `hr_riwayat_jabatan`
--
ALTER TABLE `hr_riwayat_jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku_user` (`user_id`,`sku`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `items_fk_category` (`category_id`);

--
-- Indexes for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_kategori` (`user_id`,`nama_kategori`);

--
-- Indexes for table `jurnal_details`
--
ALTER TABLE `jurnal_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurnal_entry_id` (`jurnal_entry_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `jurnal_entries`
--
ALTER TABLE `jurnal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kartu_stok_user` (`user_id`),
  ADD KEY `idx_kartu_stok_item` (`item_id`),
  ADD KEY `idx_kartu_stok_tanggal` (`tanggal`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_referensi_user` (`user_id`,`nomor_referensi`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `credit_account_id` (`credit_account_id`),
  ADD KEY `pembelian_ibfk_4` (`created_by`),
  ADD KEY `pembelian_ibfk_5` (`updated_by`);

--
-- Indexes for table `pembelian_details`
--
ALTER TABLE `pembelian_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `pembelian_details_fk_item` (`item_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_referensi_user` (`user_id`,`nomor_referensi`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `penjualan_ibfk_4` (`updated_by`);

--
-- Indexes for table `penjualan_details`
--
ALTER TABLE `penjualan_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `reconciliations`
--
ALTER TABLE `reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recurring_templates`
--
ALTER TABLE `recurring_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `next_run_date` (`next_run_date`,`is_active`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD PRIMARY KEY (`role_id`,`menu_key`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_permission` (`permission_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `journal_id` (`journal_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `kas_account_id` (`kas_account_id`),
  ADD KEY `kas_tujuan_account_id` (`kas_tujuan_account_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=606;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `anggaran`
--
ALTER TABLE `anggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `consignment_items`
--
ALTER TABLE `consignment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_ledger`
--
ALTER TABLE `general_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_absensi`
--
ALTER TABLE `hr_absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `hr_absensi_golongan`
--
ALTER TABLE `hr_absensi_golongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hr_absensi_status`
--
ALTER TABLE `hr_absensi_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hr_divisi`
--
ALTER TABLE `hr_divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hr_dokumen_karyawan`
--
ALTER TABLE `hr_dokumen_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_dokumen_perusahaan`
--
ALTER TABLE `hr_dokumen_perusahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_evaluasi_probation`
--
ALTER TABLE `hr_evaluasi_probation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_golongan_gaji`
--
ALTER TABLE `hr_golongan_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hr_jabatan`
--
ALTER TABLE `hr_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hr_jadwal_kerja`
--
ALTER TABLE `hr_jadwal_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_jatah_cuti`
--
ALTER TABLE `hr_jatah_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `hr_jenis_cuti`
--
ALTER TABLE `hr_jenis_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hr_jenis_klaim`
--
ALTER TABLE `hr_jenis_klaim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hr_kantor`
--
ALTER TABLE `hr_kantor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_karyawan`
--
ALTER TABLE `hr_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `hr_klaim`
--
ALTER TABLE `hr_klaim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_komponen_gaji`
--
ALTER TABLE `hr_komponen_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hr_kpi_indicators`
--
ALTER TABLE `hr_kpi_indicators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hr_kpi_templates`
--
ALTER TABLE `hr_kpi_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_lembur`
--
ALTER TABLE `hr_lembur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_libur_nasional`
--
ALTER TABLE `hr_libur_nasional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_lowongan`
--
ALTER TABLE `hr_lowongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_offboarding`
--
ALTER TABLE `hr_offboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_pelamar`
--
ALTER TABLE `hr_pelamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_pengajuan_cuti`
--
ALTER TABLE `hr_pengajuan_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_penggajian`
--
ALTER TABLE `hr_penggajian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `hr_penggajian_komponen`
--
ALTER TABLE `hr_penggajian_komponen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `hr_pengumuman`
--
ALTER TABLE `hr_pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_penilaian_detail`
--
ALTER TABLE `hr_penilaian_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hr_penilaian_kinerja`
--
ALTER TABLE `hr_penilaian_kinerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hr_request_surat`
--
ALTER TABLE `hr_request_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_riwayat_gaji`
--
ALTER TABLE `hr_riwayat_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hr_riwayat_jabatan`
--
ALTER TABLE `hr_riwayat_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_categories`
--
ALTER TABLE `item_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_details`
--
ALTER TABLE `jurnal_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `jurnal_entries`
--
ALTER TABLE `jurnal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelian_details`
--
ALTER TABLE `pembelian_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjualan_details`
--
ALTER TABLE `penjualan_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reconciliations`
--
ALTER TABLE `reconciliations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recurring_templates`
--
ALTER TABLE `recurring_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `accounts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounts_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accounts_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD CONSTRAINT `anggaran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `anggaran_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consignment_items`
--
ALTER TABLE `consignment_items`
  ADD CONSTRAINT `consignment_items_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consignment_items_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consignment_items_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consignment_items_ibfk_4` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD CONSTRAINT `fixed_assets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fixed_assets_ibfk_2` FOREIGN KEY (`akun_aset_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fixed_assets_ibfk_3` FOREIGN KEY (`akun_akumulasi_penyusutan_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fixed_assets_ibfk_4` FOREIGN KEY (`akun_beban_penyusutan_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `hr_absensi`
--
ALTER TABLE `hr_absensi`
  ADD CONSTRAINT `fk_hr_absensi_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `hr_karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_karyawan`
--
ALTER TABLE `hr_karyawan`
  ADD CONSTRAINT `fk_hr_karyawan_atasan` FOREIGN KEY (`atasan_id`) REFERENCES `hr_karyawan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_divisi` FOREIGN KEY (`divisi_id`) REFERENCES `hr_divisi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_golongan_gaji` FOREIGN KEY (`golongan_gaji_id`) REFERENCES `hr_golongan_gaji` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_jabatan` FOREIGN KEY (`jabatan_id`) REFERENCES `hr_jabatan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_jadwal` FOREIGN KEY (`jadwal_kerja_id`) REFERENCES `hr_jadwal_kerja` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_kantor` FOREIGN KEY (`kantor_id`) REFERENCES `hr_kantor` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_hr_karyawan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_penggajian`
--
ALTER TABLE `hr_penggajian`
  ADD CONSTRAINT `fk_hr_penggajian_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `hr_karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_fk_category` FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD CONSTRAINT `item_categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal_details`
--
ALTER TABLE `jurnal_details`
  ADD CONSTRAINT `jurnal_details_ibfk_1` FOREIGN KEY (`jurnal_entry_id`) REFERENCES `jurnal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jurnal_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `jurnal_entries`
--
ALTER TABLE `jurnal_entries`
  ADD CONSTRAINT `jurnal_entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD CONSTRAINT `fk_kartu_stok_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kartu_stok_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelian_ibfk_3` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `pembelian_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelian_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pembelian_details`
--
ALTER TABLE `pembelian_details`
  ADD CONSTRAINT `pembelian_details_fk_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembelian_details_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `penjualan_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `penjualan_details`
--
ALTER TABLE `penjualan_details`
  ADD CONSTRAINT `penjualan_details_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `reconciliations`
--
ALTER TABLE `reconciliations`
  ADD CONSTRAINT `reconciliations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reconciliations_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD CONSTRAINT `fk_role_menus_role_v2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `suppliers_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `suppliers_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`kas_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_4` FOREIGN KEY (`kas_tujuan_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
