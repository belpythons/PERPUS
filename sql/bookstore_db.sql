-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 12:35 PM
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
-- Database: `bookstore_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `admin_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'Create Category', 'Added new category: Business', '2025-07-18 10:15:00'),
(2, 1, 'Create Category', 'Added new category: Health & Wellness', '2025-07-18 10:16:00'),
(3, 1, 'Create Category', 'Added new category: Education', '2025-07-18 10:17:00'),
(4, 1, 'Create Category', 'Added new category: Religion & Spirituality', '2025-07-18 10:18:00'),
(5, 1, 'Create Book', 'Added book: Laskar Pelangi', '2025-07-18 10:30:00'),
(6, 1, 'Create Book', 'Added book: Clean Code', '2025-07-18 11:00:00'),
(7, 1, 'Create Book', 'Added book: Steve Jobs Biography', '2025-07-18 11:25:00'),
(8, 1, 'Update Transaction', 'Updated transaction #1 to completed', '2025-07-18 16:00:00'),
(9, 1, 'Update Transaction', 'Updated transaction #2 to completed', '2025-07-18 18:00:00'),
(10, 1, 'Update Book', 'Updated stock for Laravel untuk Pemula', '2025-07-18 20:30:00'),
(11, 1, 'Update Transaction', 'Updated transaction #3 to completed', '2025-07-18 21:00:00'),
(12, 1, 'Create Book', 'Added book: Rich Dad Poor Dad', '2025-07-19 08:00:00'),
(13, 1, 'Update Transaction', 'Updated transaction #5 to completed', '2025-07-19 12:00:00'),
(14, 1, 'Update Book', 'Updated price for Sapiens', '2025-07-19 15:30:00'),
(15, 1, 'Update Transaction', 'Updated transaction #7 to completed', '2025-07-19 17:00:00'),
(16, 1, 'Create Book', 'Added book: The Power of Now', '2025-07-19 20:15:00'),
(17, 1, 'Update Transaction', 'Updated transaction #9 to completed', '2025-07-20 10:00:00'),
(18, 1, 'Update Book', 'Updated stock for multiple books', '2025-07-20 12:00:00'),
(19, 1, 'Edit User', 'Updated user: belva', '2025-07-20 12:03:12'),
(20, 1, 'Delete User', 'Deleted user: Sari Melati', '2025-07-20 12:03:21'),
(21, 1, 'Edit Category', 'Updated category: Fiction', '2025-07-20 12:04:05'),
(22, 1, 'Create Category', 'Created category: sci-fi', '2025-07-20 12:04:25'),
(23, 1, 'Create Book', 'Added book: atomic habbits', '2025-07-20 12:05:46'),
(24, 1, 'Edit User', 'Updated user: Andi Pratama', '2025-07-23 10:33:58'),
(25, 1, 'Edit User', 'Updated user: Budi Santoso', '2025-07-23 10:34:08'),
(26, 1, 'Edit User', 'Updated user: Maya Indira', '2025-07-23 10:34:16'),
(27, 1, 'Edit User', 'Updated user: Rizki Ahmad', '2025-07-23 10:34:29'),
(28, 1, 'Edit User', 'Updated user: belva', '2025-07-23 10:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category_id`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 1, 'Novel tentang perjuangan anak-anak Belitung menempuh pendidikan', 85000.00, 25, 'https://images-na.ssl-images-amazon.com/images/P/9793062797.01.L.jpg', '2025-07-18 10:30:00'),
(2, 'Ayat-Ayat Cinta', 'Habiburrahman El Shirazy', 1, 'Novel islami tentang cinta dan kehidupan mahasiswa di Al-Azhar', 75000.00, 30, 'https://images-na.ssl-images-amazon.com/images/P/9792248069.01.L.jpg', '2025-07-18 10:35:00'),
(3, 'Dilan 1990', 'Pidi Baiq', 1, 'Novel romantis tentang kisah cinta Dilan dan Milea di Bandung', 65000.00, 40, 'https://images-na.ssl-images-amazon.com/images/P/9786020822174.01.L.jpg', '2025-07-18 10:40:00'),
(4, 'Perahu Kertas', 'Dewi Lestari', 1, 'Novel tentang pencarian jati diri dan cinta sejati', 80000.00, 20, 'https://images-na.ssl-images-amazon.com/images/P/9789792248241.01.L.jpg', '2025-07-18 10:45:00'),
(5, 'Negeri 5 Menara', 'Ahmad Fuadi', 1, 'Novel inspiratif tentang perjuangan santri meraih mimpi', 90000.00, 35, 'https://images-na.ssl-images-amazon.com/images/P/9789792248333.01.L.jpg', '2025-07-18 10:50:00'),
(6, 'Clean Code', 'Robert C. Martin', 3, 'Panduan menulis kode yang bersih dan mudah dipahami', 350000.00, 15, 'https://images-na.ssl-images-amazon.com/images/P/0132350882.01.L.jpg', '2025-07-18 11:00:00'),
(7, 'JavaScript: The Good Parts', 'Douglas Crockford', 3, 'Buku fundamental tentang JavaScript dan best practices', 275000.00, 20, 'https://images-na.ssl-images-amazon.com/images/P/0596517742.01.L.jpg', '2025-07-18 11:05:00'),
(8, 'Laravel untuk Pemula', 'Roihan Anas', 3, 'Tutorial lengkap belajar framework Laravel dari dasar', 150000.00, 25, 'https://images-na.ssl-images-amazon.com/images/P/9786020495312.01.L.jpg', '2025-07-18 11:10:00'),
(9, 'Python Crash Course', 'Eric Matthes', 3, 'Belajar pemrograman Python dari nol hingga mahir', 320000.00, 18, 'https://images-na.ssl-images-amazon.com/images/P/1593276036.01.L.jpg', '2025-07-18 11:15:00'),
(10, 'React Native in Action', 'Nader Dabit', 3, 'Panduan lengkap membangun aplikasi mobile dengan React Native', 380000.00, 12, 'https://images-na.ssl-images-amazon.com/images/P/1617294055.01.L.jpg', '2025-07-18 11:20:00'),
(11, 'Steve Jobs', 'Walter Isaacson', 4, 'Biografi lengkap pendiri Apple Inc yang visioner', 250000.00, 22, 'https://images-na.ssl-images-amazon.com/images/P/1451648537.01.L.jpg', '2025-07-18 11:25:00'),
(12, 'Elon Musk', 'Ashlee Vance', 4, 'Kisah hidup entrepreneur paling inovatif abad ini', 280000.00, 18, 'https://images-na.ssl-images-amazon.com/images/P/0062301233.01.L.jpg', '2025-07-18 11:30:00'),
(13, 'Long Walk to Freedom', 'Nelson Mandela', 4, 'Autobiografi pejuang anti-apartheid dan Presiden Afrika Selatan', 220000.00, 15, 'https://images-na.ssl-images-amazon.com/images/P/0316548189.01.L.jpg', '2025-07-18 11:35:00'),
(14, 'Rich Dad Poor Dad', 'Robert T. Kiyosaki', 5, 'Pembelajaran finansial dari dua figur ayah yang berbeda', 120000.00, 45, 'https://images-na.ssl-images-amazon.com/images/P/1612680194.01.L.jpg', '2025-07-18 11:40:00'),
(15, 'The Lean Startup', 'Eric Ries', 5, 'Metodologi membangun startup yang efisien dan inovatif', 180000.00, 28, 'https://images-na.ssl-images-amazon.com/images/P/0307887898.01.L.jpg', '2025-07-18 11:45:00'),
(16, 'Good to Great', 'Jim Collins', 5, 'Analisis perusahaan yang berhasil bertransformasi menjadi hebat', 200000.00, 20, 'https://images-na.ssl-images-amazon.com/images/P/0066620996.01.L.jpg', '2025-07-18 11:50:00'),
(17, 'Atomic Habits', 'James Clear', 5, 'Cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk', 160000.00, 35, 'https://images-na.ssl-images-amazon.com/images/P/0735211299.01.L.jpg', '2025-07-18 11:55:00'),
(18, 'Sapiens', 'Yuval Noah Harari', 2, 'Sejarah singkat umat manusia dari zaman batu hingga era digital', 195000.00, 30, 'https://images-na.ssl-images-amazon.com/images/P/0062316095.01.L.jpg', '2025-07-18 12:00:00'),
(19, 'Educated', 'Tara Westover', 2, 'Memoir tentang kekuatan pendidikan mengubah hidup', 175000.00, 22, 'https://images-na.ssl-images-amazon.com/images/P/0399590501.01.L.jpg', '2025-07-18 12:05:00'),
(20, 'The 7 Habits of Highly Effective People', 'Stephen R. Covey', 2, 'Tujuh kebiasaan untuk mencapai efektivitas personal dan profesional', 140000.00, 40, 'https://images-na.ssl-images-amazon.com/images/P/1982137274.01.L.jpg', '2025-07-18 12:10:00'),
(21, 'The Blue Zones', 'Dan Buettner', 6, 'Rahasia umur panjang dari wilayah dengan harapan hidup tertinggi', 165000.00, 20, 'https://images-na.ssl-images-amazon.com/images/P/1426209487.01.L.jpg', '2025-07-18 12:15:00'),
(22, 'Mindfulness for Beginners', 'Jon Kabat-Zinn', 6, 'Panduan praktis meditasi dan mindfulness untuk pemula', 135000.00, 25, 'https://images-na.ssl-images-amazon.com/images/P/1604076585.01.L.jpg', '2025-07-18 12:20:00'),
(23, 'Make It Stick', 'Peter C. Brown', 7, 'Ilmu pembelajaran yang efektif berdasarkan riset kognitif', 155000.00, 14, 'https://images-na.ssl-images-amazon.com/images/P/0674729013.01.L.jpg', '2025-07-18 12:25:00'),
(24, 'Mindset', 'Carol S. Dweck', 7, 'Kekuatan pola pikir dalam mencapai kesuksesan', 145000.00, 30, 'https://images-na.ssl-images-amazon.com/images/P/0345472322.01.L.jpg', '2025-07-18 12:30:00'),
(25, 'The Power of Now', 'Eckhart Tolle', 8, 'Panduan spiritual untuk menemukan kedamaian batin', 125000.00, 28, 'https://images-na.ssl-images-amazon.com/images/P/1577314808.01.L.jpg', '2025-07-18 12:35:00'),
(26, 'atomic habbits', 'james clear', 7, 'buku tenang habbit', 500000.00, 43, 'https://tse3.mm.bing.net/th/id/OIP.40YdU1lR2EQdFRfSxnTERQHaHa?rs=1&pid=ImgDetMain&o=7&rm=3', '2025-07-20 12:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Fiction', 'Novel dan cerita fiksi yang menghibur dan menginspirasi\r\nmenambahkan detail/ deskripri', '2025-07-18 10:00:00'),
(2, 'Non-Fiction', 'Buku faktual, edukatif, dan informatif', '2025-07-18 10:00:00'),
(3, 'Technology', 'Buku tentang teknologi, programming, dan komputer', '2025-07-18 10:00:00'),
(4, 'Biography', 'Biografi dan autobiografi tokoh terkenal dunia', '2025-07-18 10:00:00'),
(5, 'Business', 'Buku bisnis, entrepreneurship, dan manajemen', '2025-07-18 10:00:00'),
(6, 'Health & Wellness', 'Buku kesehatan, kebugaran, dan gaya hidup sehat', '2025-07-18 10:00:00'),
(7, 'Education', 'Buku pendidikan dan pembelajaran', '2025-07-18 10:00:00'),
(8, 'Religion & Spirituality', 'Buku agama dan spiritualitas', '2025-07-18 10:00:00'),
(9, 'sci-fi', 'fiksi ilmiah', '2025-07-20 12:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'Novel yang sangat menginspirasi! Kisah perjuangan anak-anak Belitung benar-benar menyentuh hati.', '2025-07-18 15:30:00'),
(3, 2, 6, 5, 'Wajib dibaca untuk programmer! Clean Code benar-benar mengubah cara saya menulis kode.', '2025-07-18 17:20:00'),
(4, 4, 11, 5, 'Biografi Steve Jobs yang paling lengkap. Memberikan insight mendalam tentang sosok visioner ini.', '2025-07-18 18:10:00'),
(6, 5, 18, 5, 'Sapiens adalah masterpiece! Cara Harari menjelaskan sejarah manusia sangat menarik dan mudah dipahami.', '2025-07-18 20:15:00'),
(7, 4, 8, 4, 'Tutorial Laravel yang mudah diikuti. Cocok untuk pemula yang ingin belajar framework PHP.', '2025-07-19 09:20:00'),
(8, 6, 17, 5, 'Atomic Habits benar-benar praktikal. Sudah mulai menerapkan teknik-tekniknya dan hasilnya luar biasa!', '2025-07-19 10:45:00'),
(9, 2, 3, 4, 'Dilan emang romantis banget. Nostalgia masa SMA yang manis.', '2025-07-19 14:20:00'),
(10, 5, 20, 4, 'The 7 Habits ini klasik tapi tetap relevan. Banyak wisdom yang bisa diterapkan sehari-hari.', '2025-07-19 16:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(1, 2, 435000.00, 'completed', '2025-07-18 15:45:00'),
(3, 4, 530000.00, 'completed', '2025-07-18 19:15:00'),
(4, 2, 275000.00, 'pending', '2025-07-19 08:20:00'),
(5, 5, 390000.00, 'completed', '2025-07-19 11:10:00'),
(6, 6, 300000.00, 'pending', '2025-07-19 14:45:00'),
(8, 4, 160000.00, 'pending', '2025-07-19 18:30:00'),
(9, 2, 125000.00, 'completed', '2025-07-20 09:15:00'),
(10, 5, 280000.00, 'pending', '2025-07-20 11:45:00'),
(11, 7, 155000.00, 'pending', '2025-07-20 12:01:52'),
(12, 7, 525000.00, 'pending', '2025-07-20 12:02:16');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 85000.00),
(2, 1, 6, 1, 350000.00),
(4, 3, 11, 1, 250000.00),
(5, 3, 12, 1, 280000.00),
(6, 4, 7, 1, 275000.00),
(7, 5, 18, 1, 195000.00),
(8, 5, 19, 1, 175000.00),
(9, 6, 8, 2, 150000.00),
(12, 8, 17, 1, 160000.00),
(13, 9, 25, 1, 125000.00),
(14, 10, 12, 1, 280000.00),
(15, 11, 23, 1, 155000.00),
(16, 12, 19, 3, 175000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin Belva', 'admin@bookstore.com', '$2a$12$WKuaIaWHX6eeW0pnRdC7wunximNplndnKWYOU.JBRI6kVFsMBSREq', 'admin', '2025-07-18 10:00:00'),
(2, 'Andi Pratama', 'andi@bookstore.com', '$2y$10$r2H0ArhJN.ySmKW81mr8o.6wicfBtWjgE2jxjKplSFGnVFpHQpfQy', 'user', '2025-07-18 11:30:00'),
(4, 'Budi Santoso', 'budi@bookstore.com', '$2y$10$2tkIVnqBcPby5wDeBkVXhumFsfHMJra5aRJZIabK2vxeAt3eYB/3S', 'user', '2025-07-18 14:20:00'),
(5, 'Maya Indira', 'maya@bookstore.com', '$2y$10$0knOMi4tbZQgR6c6AaNnvuCWuA/RhDCJCweTRYT1UmVE8AHBcRZTC', 'user', '2025-07-18 16:10:00'),
(6, 'Rizki Ahmad', 'rizki@bookstore.com', '$2y$10$nACbv5GBqASSDwfJtoCXg.nGYVkga63cukMWdDbAP/h2Tpuq66yp2', 'user', '2025-07-19 08:30:00'),
(7, 'belva', 'belva@bookstore.com', '$2y$10$FZVGk.VZdTYJWqOTDSP0Nuz4YlE6C0Cgjryx/nDk617K7eRCdYyC.', 'user', '2025-07-20 12:00:02'),
(8, 'user', 'user@bookstore.com', '$2y$10$uudDK50byPtuqK2QpKbXmefvjPHhe9D.SjJZBomoqCr0pS/9TH3ym', 'user', '2025-07-23 10:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`id`, `user_id`, `activity_type`, `description`, `created_at`) VALUES
(1, 1, 'login', 'Admin logged in to dashboard', '2025-07-18 09:45:00'),
(2, 2, 'register', 'New user registered: Andi Pratama', '2025-07-18 11:30:00'),
(3, 2, 'login', 'User logged in', '2025-07-18 15:00:00'),
(4, 2, 'purchase', 'Purchased books: Laskar Pelangi, Clean Code', '2025-07-18 15:45:00'),
(5, 2, 'review', 'Added review for Laskar Pelangi', '2025-07-18 15:50:00'),
(9, 4, 'register', 'New user registered: Budi Santoso', '2025-07-18 14:20:00'),
(10, 4, 'login', 'User logged in', '2025-07-18 18:45:00'),
(11, 4, 'purchase', 'Purchased books: Steve Jobs, Elon Musk', '2025-07-18 19:15:00'),
(12, 2, 'login', 'User logged in', '2025-07-19 08:00:00'),
(13, 2, 'purchase', 'Purchased book: JavaScript The Good Parts', '2025-07-19 08:20:00'),
(14, 5, 'register', 'New user registered: Maya Indira', '2025-07-18 16:10:00'),
(15, 5, 'login', 'User logged in', '2025-07-19 10:45:00'),
(16, 5, 'purchase', 'Purchased books: Sapiens, Educated', '2025-07-19 11:10:00'),
(17, 6, 'register', 'New user registered: Rizki Ahmad', '2025-07-19 08:30:00'),
(18, 6, 'login', 'User logged in', '2025-07-19 14:00:00'),
(19, 6, 'purchase', 'Purchased books: Laravel untuk Pemula (2 copies)', '2025-07-19 14:45:00'),
(22, 4, 'login', 'User logged in', '2025-07-19 18:15:00'),
(23, 4, 'purchase', 'Purchased book: Atomic Habits', '2025-07-19 18:30:00'),
(24, 2, 'login', 'User logged in', '2025-07-20 09:00:00'),
(25, 2, 'purchase', 'Purchased book: The Power of Now', '2025-07-20 09:15:00'),
(26, 5, 'login', 'User logged in', '2025-07-20 11:30:00'),
(27, 5, 'purchase', 'Purchased book: Elon Musk', '2025-07-20 11:45:00'),
(28, 2, 'review', 'Added review for Clean Code', '2025-07-18 17:30:00'),
(29, 4, 'review', 'Added review for Steve Jobs', '2025-07-18 19:45:00'),
(31, 5, 'review', 'Added review for Sapiens', '2025-07-19 13:15:00'),
(32, 6, 'review', 'Added review for Atomic Habits', '2025-07-19 15:30:00'),
(33, 2, 'profile_update', 'Updated profile information', '2025-07-20 08:45:00'),
(35, 1, 'logout', 'Admin logged out', '2025-07-20 12:30:00'),
(36, 2, 'logout', 'User logged out', '2025-07-20 10:00:00'),
(37, 5, 'logout', 'User logged out', '2025-07-20 12:15:00'),
(38, 6, 'logout', 'User logged out', '2025-07-19 17:30:00'),
(0, 2, 'browse', 'Browsed Technology category', '2025-07-18 14:30:00'),
(0, 4, 'search', 'Searched for \"Steve Jobs\"', '2025-07-18 18:30:00'),
(0, 5, 'browse', 'Browsed Non-Fiction category', '2025-07-19 10:30:00'),
(0, 6, 'search', 'Searched for \"Laravel\"', '2025-07-19 13:45:00'),
(0, 2, 'logout', 'User logged out successfully', '2025-07-20 11:09:44'),
(0, 7, 'login', 'User logged in', '2025-07-20 12:00:10'),
(0, 7, 'purchase', 'Purchased book: Make It Stick', '2025-07-20 12:01:52'),
(0, 7, 'purchase', 'Purchased book: Educated', '2025-07-20 12:02:16'),
(0, 7, 'logout', 'User logged out successfully', '2025-07-20 12:02:26'),
(0, 1, 'login', 'User logged in', '2025-07-20 12:02:36'),
(0, 1, 'login', 'User logged in', '2025-07-21 13:19:00'),
(0, 1, 'logout', 'User logged out successfully', '2025-07-21 13:22:33'),
(0, 7, 'login', 'User logged in', '2025-07-21 13:23:40'),
(0, 8, 'login', 'User logged in', '2025-07-23 10:19:30'),
(0, 8, 'logout', 'User logged out successfully', '2025-07-23 10:19:34'),
(0, 1, 'login', 'User logged in', '2025-07-23 10:19:40'),
(0, 1, 'logout', 'User logged out successfully', '2025-07-23 10:19:45'),
(0, 1, 'login', 'User logged in', '2025-07-23 10:32:41'),
(0, 1, 'logout', 'User logged out successfully', '2025-07-23 10:32:45'),
(0, 8, 'login', 'User logged in', '2025-07-23 10:32:51'),
(0, 8, 'logout', 'User logged out successfully', '2025-07-23 10:32:57'),
(0, 7, 'login', 'User logged in', '2025-07-23 10:33:08'),
(0, 7, 'logout', 'User logged out successfully', '2025-07-23 10:33:21'),
(0, 1, 'login', 'User logged in', '2025-07-23 10:33:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD KEY `user_activities_ibfk_1` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `user_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
