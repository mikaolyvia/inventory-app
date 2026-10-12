-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Okt 2026 pada 12.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_musik`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Gitar & Bass', 'Akustik, elektrik, dan bass gitar'),
(2, 'Keyboard & Piano', 'Synthesizer, digital piano, dan MIDI controller'),
(3, 'Perkusi & Drum', 'Drum akustik, elektrik pad, dan simbal'),
(4, 'Audio & Mic', 'Microphone, amplifier, dan soundcard'),
(5, 'Alat Musik Petik', NULL),
(6, 'Alat Musik Pukul', NULL),
(7, 'Alat Musik Tiup', NULL),
(8, 'Alat Musik Tuts', NULL),
(9, 'Keyboard', NULL),
(10, 'Genjreng', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 5,
  `minimum_stock` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `price`, `stock`, `min_stock`, `minimum_stock`, `created_at`) VALUES
(1, 1, 'GTR-001', 'Fender Stratocaster Sunburst', 14500000.00, 4, 5, 3, '2026-10-09 17:00:36'),
(2, 1, 'GTR-002', 'Yamaha F310 Akustik', 1650000.00, 2, 5, 5, '2026-10-09 17:00:36'),
(3, 2, 'KEY-001', 'Roland XPS-10 Synthesizer', 8200000.00, 3, 5, 2, '2026-10-09 17:00:36'),
(4, 3, 'DRM-001', 'Pearl Roadshow 5-Piece Drum', 7500000.00, 4, 5, 3, '2026-10-09 17:00:36'),
(5, 1, 'GTR-AC-01', 'Gitar Akustik Yamaha', 1500000.00, 16, 5, 3, '2026-10-10 14:59:26'),
(6, 2, 'GDG-TR-01', 'Gendang Tradisional Kayu', 750000.00, 4, 5, 5, '2026-10-10 14:59:26'),
(7, 3, 'SLG-SU-01', 'Suling Sunda Bambu', 50000.00, 20, 5, 5, '2026-10-10 14:59:26'),
(8, 4, 'PNO-DIG-01', 'Piano Digital Roland', 8500000.00, 7, 5, 2, '2026-10-10 14:59:26'),
(9, 6, 'GME-006', 'Gamelan', 4500000.00, 20, 4, 5, '2026-10-12 10:04:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_suppliers`
--

CREATE TABLE `product_suppliers` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_suppliers`
--

INSERT INTO `product_suppliers` (`id`, `product_id`, `supplier_id`, `purchase_price`, `created_at`) VALUES
(3, 1, 3, 2000000.00, '2026-10-12 08:45:48'),
(4, 5, 1, 15000000.00, '2026-10-12 08:51:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('IN','OUT') NOT NULL,
  `note` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `user_id`, `type`, `note`, `quantity`, `notes`, `created_at`) VALUES
(1, 5, 1, 'IN', 'pengadaan stok', 3, NULL, '2026-10-11 16:44:01'),
(2, 5, 1, 'IN', 'pengadaan stok', 3, NULL, '2026-10-11 16:44:01'),
(3, 3, 1, 'OUT', 'penjualan', 1, NULL, '2026-10-11 16:44:13'),
(4, 1, 1, 'OUT', 'penjualan', 2, NULL, '2026-10-11 19:12:44'),
(5, 1, 1, 'OUT', 'penjualan', 2, NULL, '2026-10-11 19:12:44'),
(6, 4, 1, 'IN', 'pengadaan stok', 3, NULL, '2026-10-12 08:50:10'),
(7, 8, 1, 'IN', 'pengadaan stok', 5, NULL, '2026-10-12 08:51:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `address`) VALUES
(1, 'PT Yamaha Music Indo', '021-5551234', 'Jakarta Barat'),
(2, 'CV Fender Distributor', '022-7778899', 'Bandung'),
(3, 'Symphony Music Corp', '08123456789', NULL),
(4, 'Harmoni Tradisional', '08987654321', NULL),
(5, 'PT Yamaha Music Indonesia', '083892983417', 'Jogjakarta');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Administrator', 'admin', '2026-10-11 15:42:24'),
(2, 'staff', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Staff Toko', 'staff', '2026-10-11 15:42:24');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `product_suppliers`
--
ALTER TABLE `product_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_prod_supp` (`product_id`,`supplier_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indeks untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `product_suppliers`
--
ALTER TABLE `product_suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `product_suppliers`
--
ALTER TABLE `product_suppliers`
  ADD CONSTRAINT `product_suppliers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_suppliers_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
