<?php
/*
 * ========================================================================
 * FILE: index.php
 * DESKRIPSI: Halaman utama (landing page) aplikasi Belva Digital Library
 * FUNGSI: 
 *   - Menampilkan halaman beranda untuk visitor yang belum login
 *   - Redirect user yang sudah login ke dashboard sesuai role mereka
 *   - Menampilkan featured books terbaru yang tersedia
 *   - Menampilkan statistik website (total buku, user, kategori)
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * ========================================================================
 */

// Memulai session untuk tracking status login user
session_start();

// Include file konfigurasi database dan fungsi-fungsi helper
require_once 'config/database.php';

// ========================================================================
// SECTION: Authentication Check & Redirect
// ========================================================================
// Jika user sudah login, redirect ke dashboard sesuai role mereka
// Admin -> admin/dashboard.php | User biasa -> user/index.php
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/index.php');
    }
    exit(); // Stop execution setelah redirect
}

// ========================================================================
// SECTION: Data Fetching for Landing Page
// ========================================================================

// Mendapatkan koneksi database
$pdo = getConnection();

// Ambil 6 buku terbaru yang masih tersedia (stock > 0) untuk ditampilkan sebagai featured books
// JOIN dengan tabel categories untuk mendapatkan nama kategori
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                       LEFT JOIN categories c ON b.category_id = c.id 
                       WHERE b.stock > 0
                       ORDER BY b.created_at DESC LIMIT 6");
$stmt->execute();
$featured_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ========================================================================
// SECTION: Statistics Data for Landing Page
// ========================================================================

// Hitung total jumlah buku yang tersedia dalam sistem
$stmt_books = $pdo->prepare("SELECT COUNT(*) as total FROM books");
$stmt_books->execute();
$total_books = $stmt_books->fetch(PDO::FETCH_ASSOC)['total'];

// Hitung total jumlah user yang terdaftar (tidak termasuk admin)
$stmt_users = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stmt_users->execute();
$total_users = $stmt_users->fetch(PDO::FETCH_ASSOC)['total'];

// Hitung total jumlah kategori buku yang tersedia
$stmt_categories = $pdo->prepare("SELECT COUNT(*) as total FROM categories");
$stmt_categories->execute();
$total_categories = $stmt_categories->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belva - Your Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .footer {
            background: var(--primary-color);
            color: white;
            padding: 50px 0 30px;
        }

        /* Book Cards Styling */
        .book-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            border: none;
            height: 100%;
        }
        
        .book-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        .book-image-container {
            height: 280px;
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
            transition: transform 0.4s ease;
        }
        
        .book-card:hover .book-image {
            transform: scale(1.1);
        }
        
        .book-placeholder {
            font-size: 4rem;
            color: var(--secondary-color);
            opacity: 0.3;
        }
        
        .book-card-body {
            padding: 1.75rem;
        }
        
        .book-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            line-height: 1.3;
            height: 3.2rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .book-author {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .book-category {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
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
        
        /* Stats Section */
        .stats-section {
            background: var(--primary-color);
            color: white;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stat-number {
            display: block;
            font-size: 3rem;
            font-weight: 800;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .stat-label {
            display: block;
            font-size: 1.1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-book-open text-primary me-2"></i>
                Belva
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    <a href="auth/login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="auth/register.php" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Your Digital Library Awaits</h1>
                        <p class="hero-subtitle">Discover, borrow, and enjoy thousands of books from the comfort of your home. Join our digital library community today.</p>
                        <div class="mt-4">
                            <a href="auth/register.php" class="btn btn-primary btn-lg me-3">Start Reading</a>
                            <a href="auth/login.php" class="btn btn-outline-light btn-lg">Sign In</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-book-reader" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-dark mb-3">Why Choose Belva?</h2>
                <p class="lead text-muted">Experience the future of digital reading</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Vast Collection</h5>
                        <p class="text-muted">Access thousands of books across all genres. From classics to contemporary bestsellers.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Mobile Friendly</h5>
                        <p class="text-muted">Read anywhere, anytime. Our platform works seamlessly on all your devices.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Easy Management</h5>
                        <p class="text-muted">Track your borrowed books, reading history, and manage your account effortlessly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Books Section -->
    <?php if (!empty($featured_books)): ?>
    <section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-dark mb-3">Featured Books</h2>
                <p class="lead text-muted">Discover our latest and most popular titles</p>
            </div>
            <div class="row g-4">
                <?php foreach ($featured_books as $book): ?>
                    <div class="col-lg-4 col-md-6">
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
                                    <a href="auth/login.php" class="btn btn-primary btn-sm px-4">
                                        <i class="fas fa-book-open me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="auth/register.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Join Now to Explore More Books
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number"><?= number_format($total_books) ?>+</span>
                        <span class="stat-label">Books Available</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number"><?= number_format($total_users) ?>+</span>
                        <span class="stat-label">Active Readers</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number"><?= number_format($total_categories) ?>+</span>
                        <span class="stat-label">Categories</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Access</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-6 fw-bold mb-3">Ready to Start Your Reading Journey?</h2>
            <p class="lead mb-4">Join thousands of readers who have already discovered the joy of digital reading.</p>
            <a href="auth/register.php" class="btn btn-light btn-lg px-5">Create Account</a>
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
                    <p class="text-light">Your gateway to endless knowledge and entertainment through our comprehensive digital library platform.</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <div class="row">
                        <div class="col-6">
                            <a href="auth/login.php" class="text-light text-decoration-none d-block mb-2">Login</a>
                            <a href="auth/register.php" class="text-light text-decoration-none d-block mb-2">Register</a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="text-light text-decoration-none d-block mb-2">About</a>
                            <a href="#" class="text-light text-decoration-none d-block mb-2">Contact</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4 opacity-50">
            <div class="text-center">
                <p class="mb-0 text-light">&copy; 2024 Belva Digital Library. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
