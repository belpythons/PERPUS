<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Ambil detail user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil statistik user
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM transactions WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price), 0) as total_spent FROM transactions WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$_SESSION['user_id']]);
$total_spent = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT td.book_id) as books_purchased FROM transactions t JOIN transaction_details td ON t.id = td.transaction_id WHERE t.user_id = ? AND t.status = 'completed'");
$stmt->execute([$_SESSION['user_id']]);
$books_purchased = $stmt->fetchColumn();

// Ambil buku terakhir yang dibeli
$stmt = $pdo->prepare("
    SELECT b.title, b.author, b.image, t.created_at
    FROM transactions t 
    JOIN transaction_details td ON t.id = td.transaction_id 
    JOIN books b ON td.book_id = b.id 
    WHERE t.user_id = ? AND t.status = 'completed'
    ORDER BY t.created_at DESC 
    LIMIT 3
");
$stmt->execute([$_SESSION['user_id']]);
$recent_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses pembaruan profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Jika password diisi, update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $password, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $_SESSION['user_id']]);
    }

    $_SESSION['user_name'] = $name; // Update session name

    $success = "Profile updated successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* height: 10avh; */
            margin-top: 20vh;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 2rem;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 3rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.orders { background: var(--secondary-color); }
        .stat-icon.spent { background: var(--success-color); }
        .stat-icon.books { background: var(--warning-color); }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .form-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
        }

        .form-label {
            color: var(--dark-text);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-update {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-update:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .recent-books {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--box-shadow);
            margin-top: 2rem;
        }

        .book-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .book-item:hover {
            background-color: var(--light-bg);
            transform: translateX(5px);
        }

        .book-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1rem;
        }

        .alert-modern {
            border: none;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--success-color), #2ecc71);
            color: white;
            box-shadow: var(--box-shadow);
        }

        .section-title {
            color: var(--dark-text);
            font-weight: bold;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                padding: 1.5rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- <div class="page-header">
        <div class="container">
            <h1 class="mb-0"><i class="fas fa-user-circle me-3"></i>Profile Settings</h1>
            <p class="mb-0 mt-2 opacity-75">Manage your account information and preferences</p>
        </div>
    </div> -->

    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="mb-0 opacity-75"><?= htmlspecialchars($user['email']) ?></p>
                        <small class="opacity-75">Member since <?= date('F Y', strtotime($user['created_at'])) ?></small>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number"><?= $total_orders ?></div>
                        <div class="text-muted">Total Orders</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon spent">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-number">Rp <?= number_format($total_spent, 0, ',', '.') ?></div>
                        <div class="text-muted">Total Spent</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon books">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-number"><?= $books_purchased ?></div>
                        <div class="text-muted">Books Purchased</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="form-card">
                    <h4 class="section-title">
                        <i class="fas fa-edit"></i>
                        Update Profile Information
                    </h4>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                            <small class="form-text text-muted">Only fill this if you want to change your password</small>
                        </div>
                        <button type="submit" class="btn btn-update">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>

                <?php if (!empty($recent_books)): ?>
                    <div class="recent-books">
                        <h4 class="section-title">
                            <i class="fas fa-history"></i>
                            Recent Purchases
                        </h4>
                        <?php foreach ($recent_books as $book): ?>
                            <div class="book-item">
                                <img src="../<?= $book['image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                                    <p class="mb-1 text-muted">by <?= htmlspecialchars($book['author']) ?></p>
                                    <small class="text-muted">Purchased on <?= date('M d, Y', strtotime($book['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="history.php" class="btn btn-outline-primary mt-3">
                            <i class="fas fa-eye me-2"></i>View All Orders
                        </a>
                    </div>
                <?php else: ?>
                    <div class="recent-books text-center">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No purchases yet</h5>
                        <p class="text-muted mb-0">Start exploring our collection to see your purchase history here!</p>
                        <a href="index.php" class="btn btn-primary mt-3">
                            <i class="fas fa-book me-2"></i>Browse Books
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
