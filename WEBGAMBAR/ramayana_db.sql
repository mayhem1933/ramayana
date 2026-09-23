-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 07:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `ramayana_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
    `id` int(11) NOT NULL,
    `username` varchar(50) NOT NULL,
    `email` varchar(150) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` date NOT NULL DEFAULT(current_date())
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO
    `admins` (
        `id`,
        `username`,
        `email`,
        `password_hash`,
        `is_active`
    )
VALUES (
        1,
        'admin',
        'admin@example.com',
        '$2y$10$1IUqjQc1ADF8gtrKpRCFV.n4RHVuynK7c0.cdnSc/qvnXKL5JklKK',
        1
    );

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
    `id` int(11) NOT NULL,
    `email` varchar(150) NOT NULL,
    `logged_in_at` date NOT NULL DEFAULT(current_date())
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Indexes for table `login_logs`
--

ALTER TABLE `login_logs` ADD PRIMARY KEY (`id`);

ALTER TABLE `login_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_quota`
--

CREATE TABLE `ticket_quota` (
    `id` tinyint(3) unsigned NOT NULL,
    `max_tickets` int(10) unsigned NOT NULL DEFAULT 30,
    `sold_count` int(10) unsigned NOT NULL DEFAULT 0,
    `reset_at` date NOT NULL DEFAULT(current_date())
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
    `ticket_quota` (
        `id`,
        `max_tickets`,
        `sold_count`
    )
VALUES (1, 30, 0);

--
-- Indexes for table `ticket_quota`
--

ALTER TABLE `ticket_quota` ADD PRIMARY KEY (`id`);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
    `id` int(11) NOT NULL,
    `token` varchar(30) NOT NULL,
    `nama` varchar(100) NOT NULL,
    `kelas` varchar(20) NOT NULL,
    `jumlah_tiket` int(11) NOT NULL,
    `waktu_reservasi` varchar(100) NOT NULL,
    `nomor_handphone` varchar(30) NOT NULL,
    `email` varchar(150) NOT NULL,
    `status` varchar(30) NOT NULL DEFAULT 'belum digunakan',
    `dibuat_pada` date NOT NULL,
    `dipindai_pada` date DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO
    `tickets` (
        `id`,
        `token`,
        `nama`,
        `kelas`,
        `jumlah_tiket`,
        `waktu_reservasi`,
        `nomor_handphone`,
        `email`,
        `status`,
        `dibuat_pada`,
        `dipindai_pada`
    )
VALUES (
        13,
        'RMA-99446B7AB6',
        'hafiz',
        '12 B',
        1,
        'Sesi Siang - 13.00 WIB',
        '081383817996',
        'rizkyhaafizz01@gmail.com',
        'belum digunakan',
        '2026-09-15 14:57:17',
        NULL
    ),
    (
        14,
        'RMA-76B0C69F2C',
        'Farrel Febrian',
        '12 F',
        1,
        'Sesi Malam - 19.00 WIB',
        '081383817996',
        'reularel@gmail.com',
        'belum digunakan',
        '2026-09-15 17:13:54',
        NULL
    ),
    (
        15,
        'RMA-EA479DD06E',
        'Allure Dwifa Garnadi',
        '10 A',
        1,
        'Sesi Siang - 13.00 WIB',
        '089530191638',
        'alurdwifa@gmail.com',
        'sudah digunakan',
        '2026-09-16 03:42:34',
        '2026-09-16 03:43:07'
    ),
    (
        16,
        'RMA-BA0209B6CF',
        'Anisah Nurjalilah',
        '12 F',
        1,
        'Sesi Siang - 13.00 WIB',
        '085794234745',
        'anisahnurjalilah91@gmail.com',
        'belum digunakan',
        '2026-09-16 03:44:50',
        NULL
    );

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `username` (`username`),
ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 17;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;