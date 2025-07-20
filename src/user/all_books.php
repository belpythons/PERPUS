<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Ambil parameter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Query untuk mengambil buku
$query = "SELECT b.*, c.name as category_name FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (b.title LIKE ? OR b.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND b.category_id = ?";
    $params[] = $category;
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua kategori untuk filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books - Belva Digital Library</title>
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
            margin-top: 5vh;
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
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="80" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="30" r="1" fill="%23ffffff" opacity="0.1"/></svg>') repeat;
            pointer-events: none;
        }
        
        .page-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin: -30px 0 3rem;
            position: relative;
            z-index: 3;
        }
        
        .form-control {
            border-radius: 50px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-select {
            border-radius: 50px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            font-weight: 500;
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
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
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
        
        .stock-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .search-section {
                padding: 1.5rem;
                margin: -20px 0 2rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
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
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="all_books.php">
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="text-center">
                <h1 class="page-title">Browse All Books</h1>
                <p class="page-subtitle">Discover your next great read from our extensive collection</p>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <div class="container">
        <div class="search-section">
            <div class="row g-3">
                <div class="col-md-8">
                    <form method="GET" class="d-flex">
                        <input class="form-control me-3" type="search" name="search" placeholder="Search books or authors..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="mb-4">
            <p class="text-muted">
                <i class="fas fa-book-open me-2"></i>
                Found <?= count($books) ?> book<?= count($books) != 1 ? 's' : '' ?>
                <?php if (!empty($search)): ?>
                    for "<?= htmlspecialchars($search) ?>"
                <?php endif; ?>
                <?php if (!empty($category)): ?>
                    in category "<?= htmlspecialchars($categories[array_search($category, array_column($categories, 'id'))]['name'] ?? '') ?>"
                <?php endif; ?>
            </p>
        </div>

        <!-- Books Grid -->
        <div class="row g-4">
            <?php if (empty($books)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search" style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;"></i>
                        <h4 class="text-muted">No books found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse all categories</p>
                        <a href="all_books.php" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>View All Books
                        </a>
                    </div>
                </div>
            <?php else: ?>
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
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <?php if ($book['stock'] > 0): ?>
                                        <span class="stock-badge bg-success text-white">Available</span>
                                    <?php else: ?>
                                        <span class="stock-badge bg-danger text-white">Out of Stock</span>
                                    <?php endif; ?>
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
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Spacing -->
    <div class="pb-5"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
