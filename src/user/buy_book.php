<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Dapatkan ID buku dari parameter
$book_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$book_id) {
    die('Book ID is required.');
}

// Ambil detail buku
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                       LEFT JOIN categories c ON b.category_id = c.id 
                       WHERE b.id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die('Book not found.');
}

// Proses pembelian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = $_POST['quantity'];
    $total_price = $book['price'] * $quantity;
    
    // Cek stok
    if ($book['stock'] < $quantity) {
        $error = "Insufficient stock. Available: " . $book['stock'];
    } else {
        // Mulai transaksi
        $pdo->beginTransaction();
        
        try {
            // Insert transaksi
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, total_price, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $total_price]);
            $transaction_id = $pdo->lastInsertId();
            
            // Insert detail transaksi
            $stmt = $pdo->prepare("INSERT INTO transaction_details (transaction_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$transaction_id, $book_id, $quantity, $book['price']]);
            
            // Update stok buku
            $stmt = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$quantity, $book_id]);
            
            // Log aktivitas user
            logUserActivity($_SESSION['user_id'], 'purchase', "Purchased book: " . $book['title']);
            
            $pdo->commit();
            
            $success = "Book purchased successfully! Transaction ID: " . $transaction_id;
            
        } catch (Exception $e) {
            $pdo->rollback();
            $error = "Purchase failed. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Book - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #4facfe;
            --warning-color: #ffecd2;
            --danger-color: #ff6b6b;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --bg-light: #f7fafc;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, var(--bg-light) 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
        }

        .main-container {
            margin: 2rem auto;
            max-width: 1200px;
            padding: 0 1rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 2rem;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--shadow-color);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border: none;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-body-custom {
            padding: 2rem;
        }

        .book-info {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 2rem;
        }

        .book-info h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .info-value {
            font-weight: 500;
            color: var(--text-primary);
        }

        .price-highlight {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .stock-badge {
            background: linear-gradient(135deg, var(--success-color), #00f2fe);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
            color: white;
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(75, 192, 192, 0.1), rgba(32, 201, 151, 0.1));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.1), rgba(255, 99, 71, 0.1));
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 206, 84, 0.1));
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .card-body-custom {
                padding: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-gradient, .btn-secondary-custom {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="main-container">
    <h1 class="page-title">Purchase Book</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?= $success ?>
            <div class="mt-2">
                <a href="history.php" class="btn btn-gradient">
                    <i class="fas fa-history"></i> View Transaction History
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="modern-card">
                <div class="card-header-custom">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Purchase Details
                </div>
                <div class="card-body-custom">
                    <div class="book-info">
                        <h5><i class="fas fa-book me-2"></i><?= htmlspecialchars($book['title']) ?></h5>
                        
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-user me-2"></i>Author:</span>
                            <span class="info-value"><?= htmlspecialchars($book['author']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-tag me-2"></i>Category:</span>
                            <span class="info-value"><?= htmlspecialchars($book['category_name']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-money-bill-wave me-2"></i>Price:</span>
                            <span class="info-value price-highlight"><?= formatRupiah($book['price']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-boxes me-2"></i>Available Stock:</span>
                            <span class="stock-badge"><?= $book['stock'] ?> units</span>
                        </div>
                    </div>
                    
                    <?php if ($book['stock'] > 0): ?>
                        <form method="POST">
                            <div class="mb-4">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-sort-numeric-up me-2"></i>Quantity
                                </label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?= $book['stock'] ?>" value="1" required>
                                <div class="form-text text-muted">Maximum: <?= $book['stock'] ?> units</div>
                            </div>
                            
                            <div class="button-group">
                                <button type="submit" class="btn-gradient">
                                    <i class="fas fa-shopping-cart"></i> Purchase Now
                                </button>
                                <a href="book_detail.php?id=<?= $book['id'] ?>" class="btn-secondary-custom">
                                    <i class="fas fa-arrow-left"></i> Back to Details
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This book is currently out of stock.
                        </div>
                        <div class="button-group">
                            <a href="book_detail.php?id=<?= $book['id'] ?>" class="btn-secondary-custom">
                                <i class="fas fa-arrow-left"></i> Back to Details
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
