<?php
/*
 * ========================================================================
 * FILE: user/book_detail.php
 * DESKRIPSI: Halaman detail buku untuk user
 * FUNGSI:
 *   - Menampilkan informasi lengkap buku (judul, penulis, deskripsi, harga, dll)
 *   - Menampilkan gambar buku dengan fallback jika tidak ada
 *   - Sistem review dan rating buku
 *   - Tombol untuk membeli buku
 *   - Validasi stok sebelum pembelian
 *   - Responsive design untuk semua device
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: User Only (role = 'user')
 * ========================================================================
 */

// Memulai session untuk mengecek status login user
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// ========================================================================
// SECTION: User Access Control
// ========================================================================
// Validasi akses - hanya user dengan role 'user' yang boleh mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit(); // Stop eksekusi untuk keamanan
}

// Mendapatkan koneksi database
$pdo = getConnection();

// ========================================================================
// SECTION: Book ID Validation
// ========================================================================
// Dapatkan ID buku dari parameter URL GET
$book_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Validasi apakah book_id ada dan valid
if (!$book_id) {
    die('Book ID is required. Please select a book to view.');
}

// ========================================================================
// SECTION: Book Data Fetching
// ========================================================================
// Ambil detail buku beserta nama kategorinya
// JOIN dengan tabel categories untuk mendapatkan nama kategori
$stmt = $pdo->prepare(
    "SELECT b.*, c.name as category_name 
     FROM books b 
     LEFT JOIN categories c ON b.category_id = c.id 
     WHERE b.id = ?"
);
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Validasi apakah buku ditemukan
if (!$book) {
    die('Book not found. The requested book may have been removed or does not exist.');
}

// Handle review submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $user_id = $_SESSION['user_id'];
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    // Validation
    if ($rating < 1 || $rating > 5) {
        $error_message = 'Please select a rating between 1 and 5 stars.';
    } elseif (empty($comment)) {
        $error_message = 'Please provide a comment for your review.';
    } else {
        // Check if user already reviewed this book
        $check_stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND book_id = ?");
        $check_stmt->execute([$user_id, $book_id]);
        
        if ($check_stmt->fetch()) {
            $error_message = 'You have already reviewed this book.';
        } else {
            // Insert new review
            $insert_stmt = $pdo->prepare("INSERT INTO reviews (user_id, book_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
            
            if ($insert_stmt->execute([$user_id, $book_id, $rating, $comment])) {
                $success_message = 'Your review has been submitted successfully!';
                // Redirect to prevent form resubmission
                header('Location: book_detail.php?id=' . $book_id . '&review_added=1');
                exit();
            } else {
                $error_message = 'Error submitting your review. Please try again.';
            }
        }
    }
}

// Check if we just added a review (from redirect)
if (isset($_GET['review_added']) && $_GET['review_added'] == '1') {
    $success_message = 'Your review has been submitted successfully!';
}

// Ambil review untuk buku
$stmt = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.book_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$book_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 15px;
            --text-dark: #2d3748;
            --text-light: #718096;
            --bg-light: #f7fafc;
        }

        body {
            background: var(--bg-light);
            margin-top: 100px;
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .book-image-container {
            position: relative;
            width: 100%;
            padding-bottom: 140%; /* 7:10 aspect ratio */
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .book-image-container:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .book-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .book-image:hover {
            transform: scale(1.05);
        }

        .book-image.error {
            object-fit: contain;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
            padding: 20px;
        }

        .book-details-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .book-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .book-meta {
            margin-bottom: 1.5rem;
        }

        .book-meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .book-meta-label {
            font-weight: 600;
            color: var(--text-dark);
            min-width: 100px;
            margin-right: 1rem;
        }

        .book-meta-value {
            color: var(--text-light);
        }

        .price-tag {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: 700;
            display: inline-block;
            margin: 1rem 0;
        }

        .stock-badge {
            background: var(--secondary-gradient);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .description-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin: 2rem 0;
            border-left: 4px solid #667eea;
        }

        .description-section h5 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .btn-buy {
            background: var(--success-gradient);
            border: none;
            color: white;
            padding: 1rem 3rem;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-hover-shadow);
            color: white;
        }

        .reviews-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-top: 3rem;
        }

        .reviews-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .review-item {
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .review-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .review-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .review-author {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.1rem;
        }

        .rating .fas {
            color: #ffc107;
            margin-right: 2px;
        }

        .review-form {
            background: linear-gradient(135deg, #f6f9fc 0%, #ffffff 100%);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid #e2e8f0;
        }

        .review-form h5 {
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        /* Star Rating Styles */
        .star-rating {
            direction: rtl;
            display: inline-flex;
            flex-direction: row-reverse;
            margin-bottom: 1rem;
        }
        
        .star-rating input {
            display: none;
        }
        
        .star-rating label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s;
            margin: 0 2px;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc107;
        }
        
        .star-rating input:checked ~ label {
            color: #ffc107;
        }
        
        .star-rating input:checked + label:hover,
        .star-rating input:checked + label:hover ~ label,
        .star-rating input:checked ~ label:hover,
        .star-rating input:checked ~ label:hover ~ label,
        .star-rating label:hover ~ input:checked ~ label {
            color: #ffc107;
        }

        .btn-submit-review {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 1px solid #b8dabd;
            color: #155724;
            border-radius: var(--border-radius);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1b0b7 100%);
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: var(--border-radius);
        }

        @media (max-width: 768px) {
            .book-title {
                font-size: 2rem;
            }
            
            .book-details-card {
                padding: 1.5rem;
            }
            
            .book-meta-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .book-meta-label {
                margin-bottom: 0.25rem;
                min-width: auto;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <!-- Book Details Section -->
    <div class="row g-4">
        <!-- Book Image -->
        <div class="col-md-4">
            <div class="book-image-container">
                <?php if (!empty($book['image'])): ?>
                    <img src="<?= htmlspecialchars($book['image']) ?>" 
                         alt="<?= htmlspecialchars($book['title']) ?>" 
                         class="book-image"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x420/e2e8f0/718096?text=No+Image'; this.classList.add('error');">
                <?php else: ?>
                    <img src="https://via.placeholder.com/300x420/e2e8f0/718096?text=No+Image" 
                         alt="No image available" 
                         class="book-image error">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Book Information -->
        <div class="col-md-8">
            <div class="book-details-card">
                <h1 class="book-title"><?= htmlspecialchars($book['title']) ?></h1>
                
                <div class="book-meta">
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="fas fa-user me-2"></i>Author:</span>
                        <span class="book-meta-value"><?= htmlspecialchars($book['author']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="fas fa-tag me-2"></i>Category:</span>
                        <span class="book-meta-value"><?= htmlspecialchars($book['category_name']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="fas fa-boxes me-2"></i>Stock:</span>
                        <span class="stock-badge"><?= htmlspecialchars($book['stock']) ?> available</span>
                    </div>
                </div>
                
                <div class="price-tag">
                    <i class="fas fa-tag me-2"></i><?= formatRupiah($book['price']) ?>
                </div>
                
                <?php if (!empty($book['description'])): ?>
                <div class="description-section">
                    <h5><i class="fas fa-align-left me-2"></i>Description</h5>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="buy_book.php?id=<?= $book['id'] ?>" class="btn-buy">
                        <i class="fas fa-shopping-cart me-2"></i>Buy Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <div class="reviews-header">
            <h3><i class="fas fa-comments me-2"></i>Customer Reviews</h3>
        </div>
        
        <?php if (!empty($reviews)): ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-author">
                                <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($review['user_name']) ?>
                            </div>
                            <div class="rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star<?= $i < $review['rating'] ? '' : ' text-muted' ?>" style="<?= $i >= $review['rating'] ? 'color: #dee2e6 !important;' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-comment">
                            <?= nl2br(htmlspecialchars($review['comment'])) ?>
                        </div>
                        <div class="review-date">
                            <small class="text-muted"><i class="fas fa-calendar me-1"></i>Reviewed on <?= formatDate($review['created_at']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-reviews text-center py-4">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">No reviews yet. Be the first to review this book!</p>
            </div>
        <?php endif; ?>
        
        <!-- Add Review Form -->
        <div class="review-form">
            <h5><i class="fas fa-pen me-2"></i>Write a Review</h5>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= $success_message ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="rating" class="form-label fw-bold">Your Rating *</label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5" class="star">★</label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4" class="star">★</label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3" class="star">★</label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2" class="star">★</label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1" class="star">★</label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="comment" class="form-label fw-bold">Your Review *</label>
                    <textarea name="comment" id="comment" class="form-control" rows="5" required 
                              placeholder="Share your thoughts about this book... What did you like or dislike about it?"
                              style="border-radius: var(--border-radius); border: 1px solid #e2e8f0; padding: 1rem;"></textarea>
                </div>
                
                <button type="submit" name="submit_review" class="btn-submit-review">
                    <i class="fas fa-paper-plane me-2"></i>Submit Review
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
