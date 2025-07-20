<?php
/*
 * ========================================================================
 * FILE: user/index.php
 * DESKRIPSI: Dashboard utama untuk user yang sudah login
 * FUNGSI:
 *   - Menampilkan welcome message untuk user yang login
 *   - Menampilkan 8 buku terbaru dalam format card yang menarik
 *   - Menyediakan navigasi ke fitur-fitur user (catalog, history, profile)
 *   - Interface utama untuk browsing dan discovery buku
 *   - Responsive design untuk mobile dan desktop
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: User Only (role = 'user')
 * ========================================================================
 */

// Memulai session untuk mengecek status login user
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// Note: formatRupiah function sudah tersedia di database.php
// Jadi tidak perlu didefinisikan ulang di sini

// ========================================================================
// SECTION: User Access Control
// ========================================================================
// Validasi akses - hanya user dengan role 'user' yang boleh mengakses halaman ini
// Admin tidak bisa mengakses halaman user dashboard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit(); // Stop eksekusi untuk keamanan
}

// ========================================================================
// SECTION: Featured Books Data Fetching
// ========================================================================
// Mendapatkan koneksi database
$pdo = getConnection();

// Ambil 8 buku terbaru untuk ditampilkan di dashboard user
// JOIN dengan tabel categories untuk menampilkan nama kategori
// ORDER BY created_at DESC untuk mengurutkan dari yang terbaru
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                       LEFT JOIN categories c ON b.category_id = c.id 
                       ORDER BY b.created_at DESC LIMIT 8");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belva - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: #fafbfc;
        }
        
        /* Navigation Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(15px);
            box-shadow: 0 2px 25px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.75rem;
            color: var(--primary-color) !important;
            text-decoration: none;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: #2c3e50 !important;
            margin: 0 0.5rem;
            padding: 0.7rem 1.2rem !important;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white !important;
            transform: translateY(-2px);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 15px;
            padding: 1rem 0;
        }
        
        .dropdown-item {
            padding: 0.7rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 120px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="80" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="30" r="1" fill="%23ffffff" opacity="0.1"/></svg>') repeat;
            pointer-events: none;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .hero-image {
            position: relative;
            z-index: 2;
        }
        
        /* Button Styles */
        .btn {
            font-weight: 600;
            border-radius: 50px;
            padding: 12px 30px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-light {
            background: white;
            color: var(--primary-color);
            border: 2px solid white;
        }
        
        .btn-light:hover {
            background: transparent;
            color: white;
            border-color: white;
        }
        
        /* Book Cards */
        .book-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            border: none;
            height: 100%;
            position: relative;
        }
        
        .book-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--card-hover-shadow);
        }
        
        .book-image-container {
            height: 300px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .book-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .book-card:hover .book-image {
            transform: scale(1.05);
        }
        
        .book-placeholder {
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.3;
            text-align: center;
        }
        
        .book-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(231, 76, 60, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .book-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8));
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .book-card:hover .book-overlay {
            opacity: 1;
        }
        
        .book-quick-view {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .book-card:hover .book-quick-view {
            transform: translateY(0);
        }
        
        .book-card-body {
            padding: 1.75rem;
        }
        
        .book-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            height: 3.2rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .book-author {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .book-category {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .book-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }
        
        .book-stock {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Section Styling */
        .section-padding {
            padding: 100px 0;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 4rem;
        }
        
        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 4rem 0 2rem;
        }
        
        .footer h5, .footer h6 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .book-card {
                margin-bottom: 2rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section {
                padding: 80px 0 60px;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .section-padding {
                padding: 60px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open me-2"></i>
                Belva
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_books.php">
                            <i class="fas fa-book me-1"></i> Browse Books
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags me-1"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">
                            <i class="fas fa-history me-1"></i> My Orders
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['user_name'] ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Welcome back, <?= $_SESSION['user_name'] ?>!</h1>
                        <p class="hero-subtitle">Discover your next great read from our extensive collection of books. Your literary journey continues here.</p>
                        <div class="mt-4">
                            <a href="all_books.php" class="btn btn-light btn-lg me-3">
                                <i class="fas fa-book me-2"></i> Browse All Books
                            </a>
                            <a href="categories.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-tags me-2"></i> Categories
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image text-center">
                        <i class="fas fa-book-reader" style="font-size: 12rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Latest Books</h2>
                <p class="section-subtitle">Discover our newest additions and popular titles</p>
            </div>
            <div class="row g-4">
                <?php foreach ($books as $book): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="book-card">
                            <div class="book-image-container">
                                <?php if (!empty($book['image'])): ?>
                                    <img src="<?= htmlspecialchars($book['image']) ?>" 
                                         alt="<?= htmlspecialchars($book['title']) ?>" 
                                         class="book-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="book-placeholder" style="display: none;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="book-placeholder">
                                        <i class="fas fa-book"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="book-overlay">
                                    <button class="book-quick-view btn" onclick="window.location.href='book_detail.php?id=<?= $book['id'] ?>'">
                                        <i class="fas fa-eye me-2"></i>Quick View
                                    </button>
                                </div>
                            </div>
                            <div class="book-card-body">
                                <h5 class="book-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="book-author">by <?= htmlspecialchars($book['author']) ?></p>
                                <?php if ($book['category_name']): ?>
                                    <span class="book-category"><?= htmlspecialchars($book['category_name']) ?></span>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="book-price"><?= formatRupiah($book['price']) ?></div>
                                    <small class="book-stock">Stock: <?= $book['stock'] ?></small>
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="book_detail.php?id=<?= $book['id'] ?>" class="btn btn-primary btn-sm px-4">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="all_books.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-book-open me-2"></i>Explore All Books
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-book-open me-2"></i>
                        Belva Digital Library
                    </h5>
                    <p>Your gateway to endless knowledge and entertainment through our comprehensive digital library platform.</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <div class="row">
                        <div class="col-6">
                            <a href="all_books.php" class="text-light text-decoration-none d-block mb-2">All Books</a>
                            <a href="categories.php" class="text-light text-decoration-none d-block mb-2">Categories</a>
                        </div>
                        <div class="col-6">
                            <a href="profile.php" class="text-light text-decoration-none d-block mb-2">Profile</a>
                            <a href="history.php" class="text-light text-decoration-none d-block mb-2">History</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-50">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Belva Digital Library. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
