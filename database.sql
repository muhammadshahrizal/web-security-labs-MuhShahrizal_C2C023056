-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 01:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_keamanan_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 1, 1, '<script>alert(\"XSS\")</script>', '2025-11-07 12:48:17');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `user_id`, `amount`, `description`) VALUES
(1, 1, 0.00, 'Invoice default untuk (Aman) Mayda'),
(2, 2, 0.00, 'Invoice default untuk (Aman) Riazl'),
(3, 1, 0.00, 'Invoice default untuk (Rentan) Mira');

-- --------------------------------------------------------

--
-- Table structure for table `sqli_users_safe`
--

CREATE TABLE `sqli_users_safe` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sqli_users_safe`
--

INSERT INTO `sqli_users_safe` (`id`, `username`, `password`, `fullname`, `created_at`) VALUES
(1, 'Mayda', '$2y$10$ufHUf8ZkHM.U/0cN0fU9/.1EE.MMeEse7.5TvdXu9gpg9vabWZYye', 'Maydatul', '2025-11-07 12:35:12'),
(2, 'Riazl', '$2y$10$w5pfSe.Z4AgroxVMy2nLjuohN7/pYwBiWKLz4fWzhHVLf.61wphQm', 'Shahrizal', '2025-11-07 12:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `sqli_users_vul`
--

CREATE TABLE `sqli_users_vul` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sqli_users_vul`
--

INSERT INTO `sqli_users_vul` (`id`, `username`, `password`, `fullname`, `created_at`) VALUES
(1, 'Mira', '123456789', 'Viva Miranda', '2025-11-07 12:35:39');

-- --------------------------------------------------------

--
-- Table structure for table `upload_articles`
--

CREATE TABLE `upload_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_articles`
--

INSERT INTO `upload_articles` (`id`, `title`, `body`, `file_path`, `author_id`, `created_at`) VALUES
(1, 'Unimus Gelar Wisuda ke-47, Lahirkan 1.576 Lulusan Unggul Siap mengabdi Untuk bangsa', 'Semarang | Universitas Muhammadiyah Semarang (Unimus) kembali menyelenggarakan Wisuda ke–47 periode Oktober 2025. Acara berlangsung khidmat selama dua hari, 28–29 Oktober 2025, di Gedung Serba Guna (GSG) Unimus, dan dibagi menjadi tiga sesi. Kegiatan ini dihadiri oleh Rektor (Prof. Dr. Masrukhi, M.Pd.), Senat Universitas, Kepala LLDIKTI Wilayah VI Jawa Tengah (Prof. Dr. Ir. Aisyah Endah Palupi, M.Pd), Ketua Badan Pembina Harian (Ir. Heru Isanawan, M.M.), pimpinan fakultas dan lembaga di lingkungan Unimus, serta orang tua dan wali wisudawan.\r\n\r\nPada wisuda kali ini, Unimus secara resmi melepaskan 1.576 lulusan, yang terdiri dari 167 lulusan program Profesi (Profesi Dokter, Dokter Gigi, Bidan, dan Ners), 71 lulusan program Pascasarjana, 896 lulusan program Sarjana, dan 442 lulusan program Diploma. Dengan bertambahnya jumlah tersebut, hingga kini Unimus telah meluluskan total 28.051 alumni yang siap mengabdi dan berkontribusi di berbagai bidang profesi.\r\n\r\nRektor Unimus, Prof. Dr. Masrukhi, M.Pd., dalam sambutannya menyampaikan bahwa mutu lulusan Unimus terus menunjukkan peningkatan yang signifikan. Berdasarkan capaian akademik, sebanyak 72,27% lulusan meraih predikat Cumlaude, sementara 27,28% lainnya lulus dengan predikat Sangat Memuaskan.\r\n\r\nDari 1.576 Lulusan sebagai bentuk apresiasi atas capaian akademik yang gemilang, Unimus menetapkan 24 wisudawan terbaik dari 24 program studi berdasarkan Keputusan Rektor Nomor 134/UNIMUS/SK.EP/2025 yang dibagi dalam 3 sesi. Beberapa di antaranya adalah pada sesi satu Pretti Murniafi (S2 Ilmu Laboratorium Klinis) IPK 3.96, Herman (S2 Keperawatan) IPK 3.95, Siti Nur Fadillah (S1 Kesehatan Masyarakat) IPK 3.97, Malisa Nisaul Alim (S1 Kedokteran) IPK 3.60, Mona Febiyola Azizah (S1 Kedokteran Gigi) IPK 3.57.\r\n\r\nPada sesi dua diantaranya Dika Sukmawati (S1 Ilmu Keperawatan) IPK 3.66, Ayu Atika Putri (S1 Kebidanan) IPK 3.75, Aina Nabila (S1 Pendidikan Matematika) IPK 3.82, Anis Priyanti (S1 Pendidikan Kimia) IPK 3.88, Zulfa Syauqi Fittaqi (S1 Teknik Mesin) IPK 3.68, Ahnaf Za’im Izzuddin (S1 Rekayasa Elektro) IPK 3.88, Fanni Tyasari (S1 Informatika) IPK 3.92, Aisyah Fitriana (S1 Manajemen) IPK 3.86, Devita Handayani (S1 Akuntansi) IPK 3.88, Shafa Alyanabila (S1 Pendidikan Bahasa Inggris) IPK 3.88, Abdigusti Rachman Maulana (S1 Sastra Inggris) IPK 3.85, Riya Kirani Setya Dewi (S1 Ilmu Gizi) IPK 3.90.\r\n\r\nPada sesi tiga (29 Oktober 2025) diawali oleh Saily Roshina Ayu Vidiana (S1 Statistika) dengan IPK 3,86, Nurul Hayati (S1 Teknologi Pangan) dengan IPK 3,94 , Neflin Dian Fanesa (D4 Teknologi Laboratorium Medis) IPK  3,95, Safira Frisca Aldyan (D3 Keperawatan) IPK 3,71, Nilam Syarifatul Ula (D3 Gizi) IPK 3,97, Ayasha Reina Metalia Nafiah (D3 Teknologi Laboratorium Medis) IPK 3,93 dan Dian Yunita Sari (D3 Kebidanan) dengan IPK 3,82.\r\n\r\nMomentum wisuda kali ini terasa semakin bermakna karena bertepatan dengan peringatan Hari Sumpah Pemuda, 28 Oktober 2025. Dalam pidatonya, Rektor Prof. Masrukhi menegaskan bahwa semangat persatuan, keilmuan, dan pengabdian yang terkandung dalam Sumpah Pemuda selaras dengan nilai-nilai pendidikan di Unimus.\r\n\r\n“Hari ini, semangat Sumpah Pemuda kita maknai kembali. Setiap wisudawan Unimus adalah penerus perjuangan bangsa, bukan lagi dengan bambu runcing, melainkan dengan ilmu pengetahuan, karakter, dan nilai Islam berkemajuan,” ujar Prof. Masrukhi. Ia juga berpesan agar para lulusan dapat menjadi generasi muda yang berintegritas, kreatif, dan inovatif dalam menghadapi tantangan global.\r\n\r\nDalam kesempatan tersebut, Prof. Masrukhi juga menyampaikan capaian membanggakan Unimus yang kini telah meraih Akreditasi Institusi Perguruan Tinggi (AIPT) dengan predikat “UNGGUL”. Unimus memiliki 8 fakultas dan program pascasarjana dengan total 39 program studi, serta berhasil meraih penghargaan dari LLDIKTI Wilayah VI Jawa Tengah sebagai PTS dengan Kinerja dan Reputasi Penelitian Terprogresif, dan PTS dengan Pengabdian Masyarakat (Abdimas) Terbaik. Capaian tersebut menegaskan komitmen Unimus untuk terus berkembang sebagai institusi pendidikan tinggi yang inovatif, adaptif, dan berdaya saing global.\r\n\r\nSemantara itu Kepala LLDIKTI Wilayah VI Jawa Tengah Dalam sambutannya, menegaskan bahwa lulusan Unimus adalah generasi yang ditunggu kontribusinya oleh bangsa. “Hari ini bukan akhir, melainkan awal dari perjalanan panjang. Persaingan dunia kerja semakin ketat, karena itu lulusan harus mampu berpikir out of the box dan mengimplementasikan ilmunya untuk masyarakat,” pesannya.\r\n\r\nDengan semangat “Bersatu, Bangkit, dan Tumbuh”, Unimus berkomitmen untuk terus mencetak lulusan yang tidak hanya unggul dalam bidang akademik, tetapi juga berkarakter, berakhlak, dan siap menjadi agen perubahan bagi kemajuan bangsa dan dunia.\r\n\r\n\r\n', 'uploads/safe_690dea6123428-WhatsApp-Image-2025-02-27-at-16.24.53-8-1536x1023.jpeg', 1, '2025-11-07 12:47:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sqli_users_safe`
--
ALTER TABLE `sqli_users_safe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `sqli_users_vul`
--
ALTER TABLE `sqli_users_vul`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `upload_articles`
--
ALTER TABLE `upload_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sqli_users_safe`
--
ALTER TABLE `sqli_users_safe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sqli_users_vul`
--
ALTER TABLE `sqli_users_vul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `upload_articles`
--
ALTER TABLE `upload_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `upload_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `sqli_users_safe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upload_articles`
--
ALTER TABLE `upload_articles`
  ADD CONSTRAINT `upload_articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `sqli_users_safe` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
