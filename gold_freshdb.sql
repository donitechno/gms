-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Sep 2018 pada 06.00
-- Versi server: 10.1.32-MariaDB
-- Versi PHP: 7.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gold`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_bayar_non_tunai`
--

CREATE TABLE `gold_bayar_non_tunai` (
  `id` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  `account_number` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_box`
--

CREATE TABLE `gold_box` (
  `id` int(11) NOT NULL,
  `nama_box` int(11) NOT NULL,
  `pesanan` varchar(1) NOT NULL DEFAULT 'N',
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_box`
--

INSERT INTO `gold_box` (`id`, `nama_box`, `pesanan`, `status`) VALUES
(1, 1, 'N', 'A'),
(2, 2, 'N', 'A'),
(3, 3, 'N', 'A'),
(4, 4, 'N', 'A'),
(5, 5, 'N', 'A'),
(6, 6, 'N', 'A'),
(7, 7, 'N', 'A'),
(8, 8, 'N', 'A'),
(9, 9, 'N', 'A'),
(10, 10, 'N', 'A'),
(11, 11, 'N', 'A'),
(12, 12, 'N', 'A'),
(13, 13, 'N', 'A'),
(14, 14, 'N', 'A'),
(15, 15, 'N', 'A'),
(16, 16, 'N', 'A'),
(17, 17, 'N', 'A'),
(18, 18, 'N', 'A'),
(19, 19, 'N', 'A'),
(20, 20, 'N', 'A'),
(21, 21, 'Y', 'A'),
(22, 22, 'N', 'A'),
(23, 23, 'N', 'A'),
(24, 24, 'N', 'A'),
(25, 25, 'N', 'A'),
(26, 26, 'N', 'A'),
(27, 27, 'N', 'A'),
(28, 28, 'N', 'A'),
(29, 29, 'N', 'A'),
(30, 30, 'N', 'A'),
(31, 31, 'N', 'A'),
(32, 32, 'N', 'A'),
(33, 33, 'N', 'A'),
(34, 34, 'N', 'A'),
(35, 35, 'N', 'A'),
(36, 36, 'N', 'A'),
(37, 37, 'N', 'A'),
(38, 38, 'N', 'A'),
(39, 39, 'N', 'A'),
(40, 40, 'N', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_coa_gr`
--

CREATE TABLE `gold_coa_gr` (
  `accountnumber` varchar(128) NOT NULL,
  `accountnumberint` int(11) DEFAULT NULL,
  `accountname` varchar(128) DEFAULT NULL,
  `accountgroup` int(11) DEFAULT NULL,
  `beginningbalance` double DEFAULT NULL,
  `status` text,
  `type` text,
  `idkarat` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_coa_gr`
--

INSERT INTO `gold_coa_gr` (`accountnumber`, `accountnumberint`, `accountname`, `accountgroup`, `beginningbalance`, `status`, `type`, `idkarat`, `created_date`, `created_by`) VALUES
('17-0001', 170001, 'REPARASI TOKO', 1, 0, 'A', 'SRT', 1, '0000-00-00 00:00:00', ''),
('17-0002', 170002, 'DEPARTEMEN REPARASI', 1, 0, 'A', 'SDR', 1, '0000-00-00 00:00:00', ''),
('17-0003', 170003, 'DEPARTEMEN PENGADAAN', 1, 0, 'A', 'SDG', 1, '0000-00-00 00:00:00', ''),
('17-0004', 170004, 'PAJANGAN', 1, 0, 'A', 'PJG', 1, '0000-00-00 00:00:00', ''),
('18-0001', 180001, 'SRT 916', 1, 0, 'A', 'SRT', 3, '0000-00-00 00:00:00', ''),
('18-0002', 180002, 'SRT 750', 1, 0, 'A', 'SRT', 4, '0000-00-00 00:00:00', ''),
('18-0003', 180003, 'SRT 700', 1, 0, 'A', 'SRT', 5, '0000-00-00 00:00:00', ''),
('18-0004', 180004, 'SDR 916', 1, 0, 'A', 'SDR', 3, '0000-00-00 00:00:00', ''),
('18-0005', 180005, 'SDR 750', 1, 0, 'A', 'SDR', 4, '0000-00-00 00:00:00', ''),
('18-0006', 180006, 'SDR 700', 1, 0, 'A', 'SDR', 5, '0000-00-00 00:00:00', ''),
('18-0007', 180007, 'SDG 916', 1, 0, 'A', 'SDG', 3, '0000-00-00 00:00:00', ''),
('18-0008', 180008, 'SDG 750', 1, 0, 'A', 'SDG', 4, '0000-00-00 00:00:00', ''),
('18-0009', 180009, 'SDG 700', 1, 0, 'A', 'SDG', 5, '0000-00-00 00:00:00', ''),
('18-0010', 180010, 'PJG 916', 1, 0, 'A', 'PJG', 3, '0000-00-00 00:00:00', ''),
('18-0011', 180011, 'PJG 750', 1, 0, 'A', 'PJG', 4, '0000-00-00 00:00:00', ''),
('18-0012', 180012, 'PJG 700', 1, 0, 'A', 'PJG', 5, '0000-00-00 00:00:00', ''),
('19-0001', 190001, 'PAJANGAN', 1, 0, 'A', 'PJG', 1, '0000-00-00 00:00:00', ''),
('31-0001', 310001, 'MODAL KERJA', 3, 0, 'A', 'MKI', 1, '0000-00-00 00:00:00', ''),
('41-0003', 410003, 'PENJUALAN', 1, 0, 'A', 'JL', 1, '0000-00-00 00:00:00', ''),
('41-0004', 410004, 'LEBIH/SUSUT BERAT', 4, 0, 'A', 'PD', 1, '0000-00-00 00:00:00', ''),
('51-0001', 510001, 'PEMBELIAN', 1, 0, 'A', 'BL', 1, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_coa_rp`
--

CREATE TABLE `gold_coa_rp` (
  `accountnumber` varchar(128) NOT NULL,
  `accountnumberint` int(11) DEFAULT NULL,
  `accountname` varchar(128) DEFAULT NULL,
  `accountgroup` int(11) DEFAULT NULL,
  `beginningbalance` double DEFAULT NULL,
  `status` varchar(128) DEFAULT NULL,
  `type` text,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_coa_rp`
--

INSERT INTO `gold_coa_rp` (`accountnumber`, `accountnumberint`, `accountname`, `accountgroup`, `beginningbalance`, `status`, `type`, `created_date`, `created_by`) VALUES
('11-0001', 110001, 'KAS KASIR EMAS', 1, 0, 'A', 'KK', '2018-03-28 07:12:02', 'Febrian'),
('15-0001', 150001, 'PIUTANG - KESALAHAN KERJA', 1, 0, 'A', 'PI', '2018-09-08 04:24:04', 'Febrian'),
('16-0001', 160001, 'PINJAMAN SEMENTARA', 1, 0, 'A', 'PI', '2018-09-08 04:24:39', 'Febrian'),
('17-0001', 170001, 'PIUTANG - DP BIAYA', 1, 10000, 'A', 'PIDP', '2018-08-23 04:01:47', 'Febrian'),
('21-0001', 210001, 'UANG MUKA PESANAN', 2, 0, 'A', 'UMP', '2018-05-26 02:19:39', 'Febrian'),
('31-0001', 310001, 'MODAL KERJA', 3, 0, 'A', 'MKI', '2018-08-23 03:58:25', 'Febrian'),
('41-0001', 410001, 'PENDAPATAN LAIN-LAIN', 4, 0, 'A', 'PL', '2018-08-23 04:00:28', 'Febrian'),
('41-0002', 410002, 'PENDAPATAN JASA GIRO', 4, 0, 'A', 'PL', '2018-08-23 04:00:28', 'Febrian'),
('41-0003', 410003, 'PENJUALAN', 4, 0, 'A', 'JL', '2018-03-28 07:12:02', 'Febrian'),
('51-0001', 510001, 'PEMBELIAN', 5, 0, 'A', 'BL', '2018-04-23 07:29:06', 'Febrian'),
('52-0001', 520001, 'BIAYA OPERASIONAL', 5, 0, 'A', 'BY', '2018-08-23 04:05:33', 'Febrian'),
('53-0001', 530001, 'BIAYA BERSAMA GROUP', 5, 0, 'A', 'BY', '2018-08-23 04:05:33', 'Febrian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_customer`
--

CREATE TABLE `gold_customer` (
  `cust_phone` varchar(128) NOT NULL,
  `cust_address` varchar(128) NOT NULL,
  `cust_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_customer_pesanan`
--

CREATE TABLE `gold_customer_pesanan` (
  `cust_phone` varchar(128) NOT NULL,
  `cust_address` varchar(128) NOT NULL,
  `cust_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_dailyopen`
--

CREATE TABLE `gold_dailyopen` (
  `id` int(11) NOT NULL,
  `do_date` datetime NOT NULL,
  `harga_emas` double NOT NULL,
  `created_date` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `created_by` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_dailyopen`
--

INSERT INTO `gold_dailyopen` (`id`, `do_date`, `harga_emas`, `created_date`, `last_updated`, `created_by`) VALUES
(1, '2018-09-06 00:00:00', 568000, '2018-09-06 14:47:29', '2018-09-13 10:34:00', 'Febrian'),
(2, '2018-09-05 00:00:00', 568000, '2018-09-13 10:33:57', '2018-09-13 10:33:57', 'Febrian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_defaultaccount`
--

CREATE TABLE `gold_defaultaccount` (
  `id` int(11) NOT NULL,
  `initial` varchar(128) NOT NULL,
  `accountnumber` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_defaultaccount`
--

INSERT INTO `gold_defaultaccount` (`id`, `initial`, `accountnumber`) VALUES
(1, 'KE', '11-0001'),
(2, 'JL', '41-0003'),
(3, 'BL', '51-0001'),
(4, 'PJG', '17-0004'),
(5, 'SRT', '17-0001'),
(6, 'SDR', '17-0002'),
(7, 'SDG', '17-0003'),
(8, 'UMP', '21-0001'),
(9, 'PIDP', '17-0001'),
(10, 'BYO', '52-0001'),
(11, 'BYG', '53-0001'),
(12, 'MKRP', '31-0001'),
(13, 'MKGR', '31-0001');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_detail_pembelian`
--

CREATE TABLE `gold_detail_pembelian` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(128) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `nama_product` varchar(128) NOT NULL,
  `product_pcs` int(11) NOT NULL,
  `product_weight` double NOT NULL,
  `product_price` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT 'A',
  `persentase` double NOT NULL,
  `weight_duaempat` double NOT NULL,
  `tujuan` varchar(128) NOT NULL,
  `kirim_date` datetime NOT NULL,
  `created_kirim_date` datetime NOT NULL,
  `kirim_by` varchar(128) NOT NULL,
  `last_update_kirim` datetime NOT NULL,
  `update_kirim_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_detail_penjualan`
--

CREATE TABLE `gold_detail_penjualan` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(128) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `id_box` int(11) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `nama_product` varchar(128) NOT NULL,
  `product_desc` varchar(128) NOT NULL,
  `product_weight` double NOT NULL,
  `product_price` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_detail_pesanan`
--

CREATE TABLE `gold_detail_pesanan` (
  `id` int(11) NOT NULL,
  `id_pesanan` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `nama_pesanan` varchar(128) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `product_weight` double NOT NULL,
  `harga_jual` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `box_date` datetime NOT NULL,
  `box_by` varchar(128) NOT NULL,
  `box_created_date` datetime NOT NULL,
  `ambil_date` datetime NOT NULL,
  `ambil_by` varchar(128) NOT NULL,
  `ambil_created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `updated_by` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_detail_trans_cabang`
--

CREATE TABLE `gold_detail_trans_cabang` (
  `id` int(11) NOT NULL,
  `transaction_code` varchar(128) NOT NULL,
  `cabang` varchar(2) NOT NULL,
  `tipe` varchar(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `berat_real` double NOT NULL,
  `berat_konversi` double NOT NULL,
  `persentase` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_do_formula`
--

CREATE TABLE `gold_do_formula` (
  `id` int(11) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `kadar_jual` double NOT NULL,
  `kadar_beli_bgs` double NOT NULL,
  `kadar_beli_std` double NOT NULL,
  `tampil_struk` varchar(1) NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_do_formula`
--

INSERT INTO `gold_do_formula` (`id`, `id_karat`, `description`, `kadar_jual`, `kadar_beli_bgs`, `kadar_beli_std`, `tampil_struk`, `last_updated`) VALUES
(1, 1, 'Emas 24K, berat lebih dari 31 gram', 105.1, 100, 99.327, 'N', '2018-03-09 00:00:00'),
(2, 1, 'Emas 24K, berat 16 s/d 30 gram', 106.07, 100, 99.327, 'N', '2018-03-09 00:00:00'),
(3, 1, 'Emas 24K, berat 4 s/d 15 gram', 108.1, 100, 99.327, 'N', '2018-03-09 00:00:00'),
(4, 1, 'Emas 24K, berat s/d 3 gram', 111.06, 100, 99.327, 'Y', '2018-03-09 00:00:00'),
(5, 3, 'Emas 916', 106.27, 95.8, 87.87, 'Y', '2018-03-09 00:00:00'),
(6, 8, 'Emas 916 Fashion (exclusive)', 112.25, 95.8, 87.87, 'N', '2018-03-09 00:00:00'),
(7, 4, 'Emas 750', 91.4, 79.5, 70.875, 'Y', '2018-03-09 00:00:00'),
(8, 5, 'Emas 700', 84.5, 75.9, 65.99, 'Y', '2018-03-09 00:00:00'),
(9, 6, 'Berlian', 0, 0, 0, 'N', '2018-03-12 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_karat`
--

CREATE TABLE `gold_karat` (
  `id` int(11) NOT NULL,
  `karat_name` varchar(128) NOT NULL,
  `kadar` double NOT NULL,
  `status` varchar(2) NOT NULL,
  `srt` varchar(2) NOT NULL,
  `sdr` varchar(2) NOT NULL,
  `sdg` varchar(2) NOT NULL,
  `currency` varchar(128) NOT NULL,
  `do` varchar(2) NOT NULL,
  `to_reparasi` double NOT NULL,
  `from_reparasi` double NOT NULL,
  `kali_laporan` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_karat`
--

INSERT INTO `gold_karat` (`id`, `karat_name`, `kadar`, `status`, `srt`, `sdr`, `sdg`, `currency`, `do`, `to_reparasi`, `from_reparasi`, `kali_laporan`) VALUES
(1, '24K', 100, 'A', 'Y', 'Y', 'Y', 'Y', 'Y', 100, 0, 1.01),
(2, '24F', 100, 'A', 'N', 'N', 'N', 'N', 'Y', 100, 0, 0),
(3, '916', 0, 'A', 'Y', 'Y', 'Y', 'Y', 'Y', 92, 0, 1.0125),
(4, '750', 0, 'A', 'Y', 'Y', 'Y', 'Y', 'Y', 75, 0, 0.87),
(5, '700', 0, 'A', 'Y', 'Y', 'Y', 'Y', 'Y', 70, 0, 0.7825),
(6, 'BERLIAN', 0, 'A', 'Y', 'N', 'N', 'N', 'Y', 0, 0, 2.5),
(7, '24B', 0, 'A', 'N', 'N', 'N', 'N', 'Y', 0, 0, 0),
(8, '916F', 0, 'A', 'N', 'N', 'N', 'N', 'Y', 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_karyawan`
--

CREATE TABLE `gold_karyawan` (
  `username` varchar(128) NOT NULL,
  `nama_karyawan` varchar(128) NOT NULL,
  `kelompok` varchar(128) NOT NULL,
  `accountnumber` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_kasir`
--

CREATE TABLE `gold_kasir` (
  `id` int(11) NOT NULL,
  `computer_name` varchar(128) NOT NULL,
  `printer_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_kasir`
--

INSERT INTO `gold_kasir` (`id`, `computer_name`, `printer_name`) VALUES
(1, 'Gold', 'PrinterGold');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_main_pembelian`
--

CREATE TABLE `gold_main_pembelian` (
  `id` int(11) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `transaction_code` varchar(128) NOT NULL,
  `cust_service` varchar(128) NOT NULL,
  `total_price` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_main_penjualan`
--

CREATE TABLE `gold_main_penjualan` (
  `id` int(11) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `transaction_code` varchar(128) NOT NULL,
  `cust_service` varchar(128) NOT NULL,
  `cust_phone` varchar(128) NOT NULL,
  `cust_address` varchar(128) NOT NULL,
  `cust_name` varchar(128) NOT NULL,
  `total_price` double NOT NULL,
  `bayar_1` double NOT NULL,
  `bayar_2` double NOT NULL,
  `jenis_bayar_1` varchar(128) NOT NULL,
  `jenis_bayar_2` varchar(128) NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_main_pesanan`
--

CREATE TABLE `gold_main_pesanan` (
  `id_pesanan` varchar(128) NOT NULL,
  `cust_name` varchar(128) NOT NULL,
  `cust_address` varchar(128) NOT NULL,
  `cust_phone` varchar(128) NOT NULL,
  `ump_val` double NOT NULL,
  `total_trans` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `box_date` datetime NOT NULL,
  `box_by` varchar(128) NOT NULL,
  `box_created_date` datetime NOT NULL,
  `ambil_date` datetime NOT NULL,
  `ambil_by` varchar(128) NOT NULL,
  `ambil_created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `updated_by` varchar(128) NOT NULL,
  `grosir_use` double NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_master_product_name`
--

CREATE TABLE `gold_master_product_name` (
  `id` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `nama_barang` varchar(256) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_gr`
--

CREATE TABLE `gold_mutasi_gr` (
  `idsite` int(11) DEFAULT NULL,
  `idmutasi` varchar(128) NOT NULL,
  `tipemutasi` text,
  `idkarat` int(11) DEFAULT NULL,
  `fromaccount` varchar(128) DEFAULT NULL,
  `toaccount` varchar(128) DEFAULT NULL,
  `value` double DEFAULT NULL,
  `description` text,
  `transdate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `createdby` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_gr_hapus`
--

CREATE TABLE `gold_mutasi_gr_hapus` (
  `idsite` int(11) DEFAULT NULL,
  `idmutasi` varchar(128) NOT NULL,
  `tipemutasi` text,
  `idkarat` int(11) DEFAULT NULL,
  `fromaccount` varchar(128) DEFAULT NULL,
  `toaccount` varchar(128) DEFAULT NULL,
  `value` double DEFAULT NULL,
  `description` text,
  `transdate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `createdby` text,
  `deleteddate` datetime NOT NULL,
  `deletedby` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_pengadaan`
--

CREATE TABLE `gold_mutasi_pengadaan` (
  `id` int(11) NOT NULL,
  `id_pengiriman` varchar(128) NOT NULL,
  `from_buy` varchar(1) NOT NULL,
  `tipe` varchar(12) NOT NULL,
  `fromaccount` varchar(12) NOT NULL,
  `toaccount` varchar(12) NOT NULL,
  `description` varchar(128) NOT NULL,
  `dua_empat` double NOT NULL,
  `semsanam` double NOT NULL,
  `juhlima` double NOT NULL,
  `juhtus` double NOT NULL,
  `total_konv` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `last_updated` datetime NOT NULL,
  `last_updated_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_pengadaan_hapus`
--

CREATE TABLE `gold_mutasi_pengadaan_hapus` (
  `id` int(11) NOT NULL,
  `id_pengiriman` varchar(128) NOT NULL,
  `from_buy` varchar(1) NOT NULL,
  `tipe` varchar(12) NOT NULL,
  `fromaccount` varchar(12) NOT NULL,
  `toaccount` varchar(12) NOT NULL,
  `description` varchar(128) NOT NULL,
  `dua_empat` double NOT NULL,
  `semsanam` double NOT NULL,
  `juhlima` double NOT NULL,
  `juhtus` double NOT NULL,
  `total_konv` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `deleted_date` datetime NOT NULL,
  `deleted_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_reparasi`
--

CREATE TABLE `gold_mutasi_reparasi` (
  `id` int(11) NOT NULL,
  `id_pengiriman` varchar(128) NOT NULL,
  `from_buy` varchar(1) NOT NULL,
  `tipe` varchar(12) NOT NULL,
  `fromaccount` varchar(12) NOT NULL,
  `toaccount` varchar(12) NOT NULL,
  `description` varchar(128) NOT NULL,
  `dua_empat` double NOT NULL,
  `dua_empat_konv` double NOT NULL,
  `semsanam` double NOT NULL,
  `semsanam_konv` double NOT NULL,
  `juhlima` double NOT NULL,
  `juhlima_konv` double NOT NULL,
  `juhtus` double NOT NULL,
  `juhtus_konv` double NOT NULL,
  `total_konv` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL,
  `last_updated` datetime NOT NULL,
  `last_updated_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_reparasi_hapus`
--

CREATE TABLE `gold_mutasi_reparasi_hapus` (
  `id` int(11) NOT NULL,
  `id_pengiriman` varchar(128) NOT NULL,
  `from_buy` varchar(1) NOT NULL,
  `tipe` varchar(12) NOT NULL,
  `fromaccount` varchar(12) NOT NULL,
  `toaccount` varchar(12) NOT NULL,
  `description` varchar(128) NOT NULL,
  `dua_empat` double NOT NULL,
  `dua_empat_konv` double NOT NULL,
  `semsanam` double NOT NULL,
  `semsanam_konv` double NOT NULL,
  `juhlima` double NOT NULL,
  `juhlima_konv` double NOT NULL,
  `juhtus` double NOT NULL,
  `juhtus_konv` double NOT NULL,
  `total_konv` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `deleted_date` datetime NOT NULL,
  `deleted_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_rp`
--

CREATE TABLE `gold_mutasi_rp` (
  `idsite` int(11) DEFAULT NULL,
  `idmutasi` varchar(128) NOT NULL,
  `tipemutasi` text,
  `fromaccount` varchar(128) DEFAULT NULL,
  `toaccount` varchar(128) DEFAULT NULL,
  `value` double DEFAULT NULL,
  `description` text,
  `transdate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `createdby` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_mutasi_rp_hapus`
--

CREATE TABLE `gold_mutasi_rp_hapus` (
  `idsite` int(11) DEFAULT NULL,
  `idmutasi` varchar(128) NOT NULL,
  `tipemutasi` text,
  `fromaccount` varchar(128) DEFAULT NULL,
  `toaccount` varchar(128) DEFAULT NULL,
  `value` double DEFAULT NULL,
  `description` text,
  `transdate` datetime DEFAULT NULL,
  `createddate` datetime DEFAULT NULL,
  `createdby` text,
  `deleteddate` datetime NOT NULL,
  `deletedby` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_periode`
--

CREATE TABLE `gold_periode` (
  `id` int(11) NOT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  `map` double NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_periode`
--

INSERT INTO `gold_periode` (`id`, `from_date`, `to_date`, `map`, `created_date`, `created_by`) VALUES
(1, '2018-05-01 00:00:00', '2019-03-31 00:00:00', 2000.78, '2018-09-04 14:58:21', 'Febrian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_pindah_box`
--

CREATE TABLE `gold_pindah_box` (
  `id` varchar(128) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_box_from` int(11) NOT NULL,
  `id_box_to` int(11) NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_pindah_karat`
--

CREATE TABLE `gold_pindah_karat` (
  `id` varchar(128) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `id_karat_from` int(11) NOT NULL,
  `id_karat_to` int(11) NOT NULL,
  `id_box` int(11) NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_product`
--

CREATE TABLE `gold_product` (
  `id` varchar(128) NOT NULL,
  `id_lama` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_box` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `id_from` int(11) NOT NULL,
  `product_from_desc` varchar(128) NOT NULL,
  `product_name` varchar(256) NOT NULL,
  `product_weight` double NOT NULL,
  `in_date` datetime NOT NULL,
  `out_date` datetime NOT NULL,
  `id_sell` varchar(128) NOT NULL,
  `sell_desc` varchar(256) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT 'A',
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_product_category`
--

CREATE TABLE `gold_product_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_product_category`
--

INSERT INTO `gold_product_category` (`id`, `category_name`, `status`) VALUES
(1, 'KALUNG', 'A'),
(2, 'GELANG', 'A'),
(3, 'CINCIN', 'A'),
(4, 'LIONTIN', 'A'),
(5, 'ANTING', 'A'),
(7, 'TINDIK', 'A'),
(8, 'BAHAN', 'A'),
(9, 'PIN', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_product_from`
--

CREATE TABLE `gold_product_from` (
  `id` int(11) NOT NULL,
  `from_name` varchar(128) NOT NULL,
  `from_desc` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_product_from`
--

INSERT INTO `gold_product_from` (`id`, `from_name`, `from_desc`, `status`) VALUES
(1, 'REPARASI', 'DARI DEPT REPARASI', 'A'),
(2, 'GROSIR', 'DARI DEPT GROSIR', 'A'),
(3, 'PESANAN', 'DARI DEPT PRODUKSI', 'NA'),
(4, 'LAIN-LAIN', 'LAIN-LAIN', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_setting_harga`
--

CREATE TABLE `gold_setting_harga` (
  `id` int(11) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `dari_berat` double NOT NULL,
  `sampai_berat` double NOT NULL,
  `min_persen` double NOT NULL,
  `max_persen` double NOT NULL,
  `min_persen_beli` double NOT NULL,
  `max_persen_beli` double NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_setting_harga`
--

INSERT INTO `gold_setting_harga` (`id`, `id_karat`, `dari_berat`, `sampai_berat`, `min_persen`, `max_persen`, `min_persen_beli`, `max_persen_beli`, `last_updated`) VALUES
(1, 1, 0.001, 3.999, 110.23, 111.53, 95, 111.53, '2018-09-10 11:01:37'),
(2, 1, 4, 15.999, 107.45, 108.85, 95, 108.85, '2018-09-10 11:01:37'),
(3, 1, 16, 30.999, 105.56, 106.66, 95, 106.66, '2018-09-10 11:01:37'),
(4, 1, 31, 9999, 104.57, 105.77, 95, 105.77, '2018-09-10 11:01:37'),
(5, 3, 0.001, 9999, 105.56, 106.86, 80, 106.86, '2018-09-10 11:01:37'),
(6, 4, 0.001, 9999, 90.37, 91.86, 65, 91.86, '2018-09-10 11:01:37'),
(7, 5, 0.001, 9999, 83.41, 85.11, 60, 85.11, '2018-09-10 11:01:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_site`
--

CREATE TABLE `gold_site` (
  `id` int(11) NOT NULL,
  `sitecode` text,
  `sitedesc` text,
  `sitestatus` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_site`
--

INSERT INTO `gold_site` (`id`, `sitecode`, `sitedesc`, `sitestatus`) VALUES
(1, 'BBL', 'Jodoh Lama', 'A'),
(2, 'BBCU', 'Cabang Utama', 'A'),
(3, 'BBLP', 'Lucky Plaza', 'A'),
(4, 'BBMM', 'Mega Mall', 'A'),
(5, 'PBL', 'Panbill', 'A'),
(6, 'AVR', 'Aviari', 'A'),
(7, 'FND', 'Fanindo', 'A'),
(8, 'BTN', 'Botania', 'A'),
(9, 'TJG', 'Tanjung Nagoya', 'A');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_site_aktif`
--

CREATE TABLE `gold_site_aktif` (
  `id` int(11) NOT NULL,
  `id_site` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_site_aktif`
--

INSERT INTO `gold_site_aktif` (`id`, `id_site`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_stock_in`
--

CREATE TABLE `gold_stock_in` (
  `id` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_box` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `id_from` int(11) NOT NULL,
  `id_from_desc` varchar(256) NOT NULL,
  `product_name` varchar(256) NOT NULL,
  `product_weight` double NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_stock_out`
--

CREATE TABLE `gold_stock_out` (
  `id` varchar(128) NOT NULL,
  `id_product` varchar(128) NOT NULL,
  `id_karat` int(11) NOT NULL,
  `id_box` int(11) NOT NULL,
  `so_reason` varchar(128) NOT NULL,
  `trans_date` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_tanggal_aktif`
--

CREATE TABLE `gold_tanggal_aktif` (
  `id` int(11) NOT NULL,
  `id_kasir` int(11) NOT NULL,
  `tanggal_aktif` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_tanggal_aktif`
--

INSERT INTO `gold_tanggal_aktif` (`id`, `id_kasir`, `tanggal_aktif`) VALUES
(1, 1, '2018-09-06 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_titipan_gr`
--

CREATE TABLE `gold_titipan_gr` (
  `id` varchar(12) NOT NULL,
  `nama_pelanggan` varchar(128) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_titipan_rp`
--

CREATE TABLE `gold_titipan_rp` (
  `id` varchar(128) NOT NULL,
  `nama_pelanggan` varchar(128) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gold_user`
--

CREATE TABLE `gold_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `password_user` varchar(50) NOT NULL,
  `priv_kasir` varchar(1) NOT NULL,
  `priv_pembukuan` varchar(1) NOT NULL,
  `priv_manager` varchar(1) NOT NULL,
  `priv_admin` varchar(1) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `picture` varchar(128) NOT NULL,
  `status` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gold_user`
--

INSERT INTO `gold_user` (`id`, `username`, `nama_user`, `password_user`, `priv_kasir`, `priv_pembukuan`, `priv_manager`, `priv_admin`, `salt`, `picture`, `status`) VALUES
(1, 'febrian', 'Febrian', '34a1085077854f57c0f134a4bf8b2c0858ce85ed', 'Y', 'Y', 'Y', 'Y', 'iVO+JBY8', 'user.jpg', 'A'),
(2, 'doni', 'Doni Riki Putra', '0bf277104837edc7c755c9129357b4954afbc351', 'Y', 'Y', 'Y', 'Y', '6&9cX,cd', 'user.jpg', 'A'),
(3, 'prawira', 'Prawira Negara', 'f27147fdf71f5f27d0254ade6e2be7d0f231c025', 'Y', 'Y', 'Y', 'Y', '1kY4vmTy', 'user.jpg', 'A'),
(4, 'eriska', 'Eriska', '441cefa24363dd8c4c73c0dac5a7faa2aee93528', 'Y', 'Y', 'Y', 'Y', '5ccpgHQw', 'user.jpg', 'A'),
(5, 'sabbiha', 'Sabbiha Usni', 'd5f14b3727ee08a9b7b5ea9a064e61d0783937cb', 'Y', 'Y', 'Y', 'Y', 'GklQfTYT', 'user.jpg', 'A'),
(6, 'wirman', 'Wirman', 'bb90fb17ad4d034764454fa213f7074950979718', 'Y', 'Y', 'Y', 'Y', '2v4wdV.?', 'user.jpg', 'A');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `gold_bayar_non_tunai`
--
ALTER TABLE `gold_bayar_non_tunai`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_box`
--
ALTER TABLE `gold_box`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_coa_gr`
--
ALTER TABLE `gold_coa_gr`
  ADD PRIMARY KEY (`accountnumber`),
  ADD KEY `idkarat` (`idkarat`);

--
-- Indeks untuk tabel `gold_coa_rp`
--
ALTER TABLE `gold_coa_rp`
  ADD PRIMARY KEY (`accountnumber`);

--
-- Indeks untuk tabel `gold_customer`
--
ALTER TABLE `gold_customer`
  ADD PRIMARY KEY (`cust_phone`);

--
-- Indeks untuk tabel `gold_customer_pesanan`
--
ALTER TABLE `gold_customer_pesanan`
  ADD PRIMARY KEY (`cust_phone`);

--
-- Indeks untuk tabel `gold_dailyopen`
--
ALTER TABLE `gold_dailyopen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_defaultaccount`
--
ALTER TABLE `gold_defaultaccount`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_detail_pembelian`
--
ALTER TABLE `gold_detail_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karat` (`id_karat`),
  ADD KEY `transaction_code` (`transaction_code`),
  ADD KEY `id_category` (`id_category`);

--
-- Indeks untuk tabel `gold_detail_penjualan`
--
ALTER TABLE `gold_detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_box` (`id_box`),
  ADD KEY `id_karat` (`id_karat`),
  ADD KEY `transaction_code` (`transaction_code`);

--
-- Indeks untuk tabel `gold_detail_pesanan`
--
ALTER TABLE `gold_detail_pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_detail_trans_cabang`
--
ALTER TABLE `gold_detail_trans_cabang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karat` (`id_karat`);

--
-- Indeks untuk tabel `gold_do_formula`
--
ALTER TABLE `gold_do_formula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karat` (`id_karat`);

--
-- Indeks untuk tabel `gold_karat`
--
ALTER TABLE `gold_karat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_karyawan`
--
ALTER TABLE `gold_karyawan`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `gold_kasir`
--
ALTER TABLE `gold_kasir`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_main_pembelian`
--
ALTER TABLE `gold_main_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `id_kasir` (`id_kasir`);

--
-- Indeks untuk tabel `gold_main_penjualan`
--
ALTER TABLE `gold_main_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `id_kasir` (`id_kasir`);

--
-- Indeks untuk tabel `gold_main_pesanan`
--
ALTER TABLE `gold_main_pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indeks untuk tabel `gold_master_product_name`
--
ALTER TABLE `gold_master_product_name`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_category` (`id_category`);

--
-- Indeks untuk tabel `gold_mutasi_gr`
--
ALTER TABLE `gold_mutasi_gr`
  ADD PRIMARY KEY (`idmutasi`),
  ADD KEY `idkarat` (`idkarat`);

--
-- Indeks untuk tabel `gold_mutasi_gr_hapus`
--
ALTER TABLE `gold_mutasi_gr_hapus`
  ADD PRIMARY KEY (`idmutasi`),
  ADD KEY `idkarat` (`idkarat`);

--
-- Indeks untuk tabel `gold_mutasi_pengadaan`
--
ALTER TABLE `gold_mutasi_pengadaan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_mutasi_pengadaan_hapus`
--
ALTER TABLE `gold_mutasi_pengadaan_hapus`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_mutasi_reparasi`
--
ALTER TABLE `gold_mutasi_reparasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_mutasi_reparasi_hapus`
--
ALTER TABLE `gold_mutasi_reparasi_hapus`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_mutasi_rp`
--
ALTER TABLE `gold_mutasi_rp`
  ADD PRIMARY KEY (`idmutasi`);

--
-- Indeks untuk tabel `gold_mutasi_rp_hapus`
--
ALTER TABLE `gold_mutasi_rp_hapus`
  ADD PRIMARY KEY (`idmutasi`);

--
-- Indeks untuk tabel `gold_periode`
--
ALTER TABLE `gold_periode`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_pindah_box`
--
ALTER TABLE `gold_pindah_box`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_karat` (`id_karat`),
  ADD KEY `id_box_from` (`id_box_from`),
  ADD KEY `id_box_to` (`id_box_to`);

--
-- Indeks untuk tabel `gold_pindah_karat`
--
ALTER TABLE `gold_pindah_karat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_karat_from` (`id_karat_from`),
  ADD KEY `id_karat_to` (`id_karat_to`),
  ADD KEY `id_box` (`id_box`);

--
-- Indeks untuk tabel `gold_product`
--
ALTER TABLE `gold_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karat` (`id_karat`),
  ADD KEY `category` (`id_category`),
  ADD KEY `box` (`id_box`),
  ADD KEY `product_from` (`id_from`);

--
-- Indeks untuk tabel `gold_product_category`
--
ALTER TABLE `gold_product_category`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_product_from`
--
ALTER TABLE `gold_product_from`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_setting_harga`
--
ALTER TABLE `gold_setting_harga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karat` (`id_karat`);

--
-- Indeks untuk tabel `gold_site`
--
ALTER TABLE `gold_site`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_site_aktif`
--
ALTER TABLE `gold_site_aktif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_site` (`id_site`);

--
-- Indeks untuk tabel `gold_stock_in`
--
ALTER TABLE `gold_stock_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karat` (`id_karat`),
  ADD KEY `id_box` (`id_box`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_from` (`id_from`);

--
-- Indeks untuk tabel `gold_stock_out`
--
ALTER TABLE `gold_stock_out`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_karat` (`id_karat`),
  ADD KEY `id_box` (`id_box`);

--
-- Indeks untuk tabel `gold_tanggal_aktif`
--
ALTER TABLE `gold_tanggal_aktif`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_titipan_gr`
--
ALTER TABLE `gold_titipan_gr`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_titipan_rp`
--
ALTER TABLE `gold_titipan_rp`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gold_user`
--
ALTER TABLE `gold_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `gold_bayar_non_tunai`
--
ALTER TABLE `gold_bayar_non_tunai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_box`
--
ALTER TABLE `gold_box`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `gold_dailyopen`
--
ALTER TABLE `gold_dailyopen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `gold_defaultaccount`
--
ALTER TABLE `gold_defaultaccount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `gold_detail_pembelian`
--
ALTER TABLE `gold_detail_pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_detail_penjualan`
--
ALTER TABLE `gold_detail_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_detail_pesanan`
--
ALTER TABLE `gold_detail_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_detail_trans_cabang`
--
ALTER TABLE `gold_detail_trans_cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_do_formula`
--
ALTER TABLE `gold_do_formula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `gold_karat`
--
ALTER TABLE `gold_karat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `gold_main_pembelian`
--
ALTER TABLE `gold_main_pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_main_penjualan`
--
ALTER TABLE `gold_main_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_master_product_name`
--
ALTER TABLE `gold_master_product_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_mutasi_pengadaan`
--
ALTER TABLE `gold_mutasi_pengadaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_mutasi_pengadaan_hapus`
--
ALTER TABLE `gold_mutasi_pengadaan_hapus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_mutasi_reparasi`
--
ALTER TABLE `gold_mutasi_reparasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_mutasi_reparasi_hapus`
--
ALTER TABLE `gold_mutasi_reparasi_hapus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gold_periode`
--
ALTER TABLE `gold_periode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `gold_product_category`
--
ALTER TABLE `gold_product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `gold_product_from`
--
ALTER TABLE `gold_product_from`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `gold_setting_harga`
--
ALTER TABLE `gold_setting_harga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `gold_site_aktif`
--
ALTER TABLE `gold_site_aktif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `gold_tanggal_aktif`
--
ALTER TABLE `gold_tanggal_aktif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `gold_user`
--
ALTER TABLE `gold_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `gold_coa_gr`
--
ALTER TABLE `gold_coa_gr`
  ADD CONSTRAINT `gold_coa_gr_ibfk_1` FOREIGN KEY (`idkarat`) REFERENCES `gold_karat` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_detail_pembelian`
--
ALTER TABLE `gold_detail_pembelian`
  ADD CONSTRAINT `gold_detail_pembelian_ibfk_1` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_detail_pembelian_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `gold_product_category` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_do_formula`
--
ALTER TABLE `gold_do_formula`
  ADD CONSTRAINT `gold_do_formula_ibfk_1` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_master_product_name`
--
ALTER TABLE `gold_master_product_name`
  ADD CONSTRAINT `gold_master_product_name_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `gold_product_category` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_mutasi_gr`
--
ALTER TABLE `gold_mutasi_gr`
  ADD CONSTRAINT `gold_mutasi_gr_ibfk_1` FOREIGN KEY (`idkarat`) REFERENCES `gold_karat` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_mutasi_gr_hapus`
--
ALTER TABLE `gold_mutasi_gr_hapus`
  ADD CONSTRAINT `gold_mutasi_gr_hapus_ibfk_1` FOREIGN KEY (`idkarat`) REFERENCES `gold_karat` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_pindah_box`
--
ALTER TABLE `gold_pindah_box`
  ADD CONSTRAINT `gold_pindah_box_ibfk_1` FOREIGN KEY (`id_box_from`) REFERENCES `gold_box` (`id`),
  ADD CONSTRAINT `gold_pindah_box_ibfk_2` FOREIGN KEY (`id_box_to`) REFERENCES `gold_box` (`id`),
  ADD CONSTRAINT `gold_pindah_box_ibfk_3` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_pindah_box_ibfk_4` FOREIGN KEY (`id_product`) REFERENCES `gold_product` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_pindah_karat`
--
ALTER TABLE `gold_pindah_karat`
  ADD CONSTRAINT `gold_pindah_karat_ibfk_1` FOREIGN KEY (`id_box`) REFERENCES `gold_box` (`id`),
  ADD CONSTRAINT `gold_pindah_karat_ibfk_2` FOREIGN KEY (`id_karat_from`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_pindah_karat_ibfk_3` FOREIGN KEY (`id_karat_to`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_pindah_karat_ibfk_4` FOREIGN KEY (`id_product`) REFERENCES `gold_product` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_product`
--
ALTER TABLE `gold_product`
  ADD CONSTRAINT `gold_product_ibfk_1` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_product_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `gold_product_category` (`id`),
  ADD CONSTRAINT `gold_product_ibfk_3` FOREIGN KEY (`id_from`) REFERENCES `gold_product_from` (`id`),
  ADD CONSTRAINT `gold_product_ibfk_4` FOREIGN KEY (`id_box`) REFERENCES `gold_box` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_setting_harga`
--
ALTER TABLE `gold_setting_harga`
  ADD CONSTRAINT `gold_setting_harga_ibfk_1` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_site_aktif`
--
ALTER TABLE `gold_site_aktif`
  ADD CONSTRAINT `gold_site_aktif_ibfk_1` FOREIGN KEY (`id_site`) REFERENCES `gold_site` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_stock_in`
--
ALTER TABLE `gold_stock_in`
  ADD CONSTRAINT `gold_stock_in_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `gold_product_category` (`id`),
  ADD CONSTRAINT `gold_stock_in_ibfk_2` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`),
  ADD CONSTRAINT `gold_stock_in_ibfk_3` FOREIGN KEY (`id_box`) REFERENCES `gold_box` (`id`),
  ADD CONSTRAINT `gold_stock_in_ibfk_4` FOREIGN KEY (`id_from`) REFERENCES `gold_product_from` (`id`);

--
-- Ketidakleluasaan untuk tabel `gold_stock_out`
--
ALTER TABLE `gold_stock_out`
  ADD CONSTRAINT `gold_stock_out_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `gold_product` (`id`),
  ADD CONSTRAINT `gold_stock_out_ibfk_2` FOREIGN KEY (`id_box`) REFERENCES `gold_box` (`id`),
  ADD CONSTRAINT `gold_stock_out_ibfk_3` FOREIGN KEY (`id_karat`) REFERENCES `gold_karat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
