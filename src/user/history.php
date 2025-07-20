<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Ambil semua transaksi user dengan detail buku pertama untuk preview
$stmt = $pdo->prepare("
    SELECT t.*, 
           COUNT(td.id) as total_items,
           b.title as first_book_title,
           b.image as first_book_image,
           b.author as first_book_author
    FROM transactions t 
    LEFT JOIN transaction_details td ON t.id = td.transaction_id 
    LEFT JOIN books b ON td.book_id = b.id 
    WHERE t.user_id = ? 
    GROUP BY t.id 
    ORDER BY t.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Belva Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        
        /* Transaction Cards */
        .transaction-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .transaction-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }
        
        .transaction-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .transaction-id {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .transaction-date {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .transaction-body {
            padding: 1.5rem;
        }
        
        .transaction-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .transaction-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .transaction-status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .book-preview {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .book-image-preview {
            width: 60px;
            height: 80px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            overflow: hidden;
        }
        
        .book-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-placeholder-small {
            font-size: 1.5rem;
            color: var(--primary-color);
            opacity: 0.3;
        }
        
        .book-info {
            flex: 1;
        }
        
        .book-title-preview {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .book-author-preview {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .transaction-items {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-bottom: none;
            border-radius: 20px 20px 0 0;
        }
        
        .modal-title {
            font-weight: 700;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .navbar-nav .nav-link {
                margin: 0.25rem 0;
            }
            
            .transaction-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .transaction-price {
                margin-bottom: 0.5rem;
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
                        <a class="nav-link active" href="history.php">
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
                <h1 class="page-title">My Orders</h1>
                <p class="page-subtitle">View your transaction history and order details</p>
            </div>
        </div>
    </section>

    <!-- Transaction History -->
    <div class="container" style="margin: 3rem 0;">
        <?php if (empty($transactions)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;"></i>
                <h4 class="text-muted">No orders found</h4>
                <p class="text-muted">You haven't made any purchases yet. Start exploring our book collection!</p>
                <a href="all_books.php" class="btn btn-primary">
                    <i class="fas fa-book me-2"></i>Browse Books
                </a>
            </div>
        <?php else: ?>
            <div class="mb-4">
                <p class="text-muted">
                    <i class="fas fa-history me-2"></i>
                    You have <?= count($transactions) ?> order<?= count($transactions) != 1 ? 's' : '' ?> in your history
                </p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="transaction-card">
                            <div class="transaction-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="transaction-id">#<?= $transaction['id'] ?></div>
                                        <div class="transaction-date"><?= formatDate($transaction['created_at']) ?></div>
                                    </div>
                                    <span class="transaction-status <?= $transaction['status'] === 'completed' ? 'status-completed' : ($transaction['status'] === 'pending' ? 'status-pending' : 'status-cancelled') ?>">
                                        <?= ucfirst($transaction['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="transaction-body">
                                <?php if ($transaction['first_book_title']): ?>
                                    <div class="book-preview">
                                        <div class="book-image-preview">
                                            <?php if (!empty($transaction['first_book_image'])): ?>
                                                <img src="<?= htmlspecialchars($transaction['first_book_image']) ?>" 
                                                     alt="<?= htmlspecialchars($transaction['first_book_title']) ?>"
                                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\"fas fa-book book-placeholder-small\"></i>';">
                                            <?php else: ?>
                                                <i class="fas fa-book book-placeholder-small"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="book-info">
                                            <div class="book-title-preview"><?= htmlspecialchars($transaction['first_book_title']) ?></div>
                                            <div class="book-author-preview">by <?= htmlspecialchars($transaction['first_book_author']) ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="transaction-items">
                                    <i class="fas fa-shopping-bag me-1"></i>
                                    <?= $transaction['total_items'] ?> item<?= $transaction['total_items'] != 1 ? 's' : '' ?>
                                </div>
                                
                                <div class="transaction-info">
                                    <div class="transaction-price"><?= formatRupiah($transaction['total_price']) ?></div>
                                </div>
                                
                                <div class="text-center">
                                    <button class="btn btn-primary btn-sm" onclick="viewTransactionDetail(<?= $transaction['id'] ?>)">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer Spacing -->
    <div class="pb-5"></div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()"><i class="fas fa-print"></i> Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentTransactionData = null;
    
    function viewTransactionDetail(id) {
        // Show loading state
        const modal = new bootstrap.Modal(document.getElementById('transactionDetailModal'));
        document.getElementById('transactionDetailContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        modal.show();
        
        // Use jQuery AJAX to fetch transaction details
        $.ajax({
            url: '/belva/user/transaction_detail.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                if (data && data.transaction && data.items) {
                    currentTransactionData = data;
                    displayTransactionDetail(data);
                } else {
                    document.getElementById('transactionDetailContent').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: Invalid response format</div>';
                    console.error('Invalid response format:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'Error loading transaction details';
                if (xhr.status === 404) {
                    errorMessage = 'Transaction details not found (404)';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred (500)';
                } else if (xhr.status === 0) {
                    errorMessage = 'Connection error - unable to reach server';
                }
                
                document.getElementById('transactionDetailContent').innerHTML = 
                    `<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${errorMessage}
                        <br><small>Status: ${xhr.status} - ${error}</small>
                    </div>`;
            }
        });
    }
    
    function displayTransactionDetail(data) {
        const transaction = data.transaction;
        const items = data.items;
        const user = data.user;
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Transaction Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>ID:</strong></td><td>${transaction.id}</td></tr>
                        <tr><td><strong>Date:</strong></td><td>${transaction.created_at}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-${transaction.status === 'completed' ? 'success' : 'warning'}">${transaction.status}</span></td></tr>
                        <tr><td><strong>Total:</strong></td><td><strong>Rp ${Number(transaction.total_price).toLocaleString('id-ID')}</strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Name:</strong></td><td>${user.name}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${user.email}</td></tr>
                    </table>
                </div>
            </div>
            
            <hr>
            
            <h6>Items Purchased</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        
                    </tr>
                </thead>
                <tbody>
        `;
        
        items.forEach(item => {
            html += `
                <tr>
                    <td>${item.title}</td>
                    <td>${item.author}</td>
                    <td>Rp ${Number(item.book_price).toLocaleString('id-ID')}</td>
                    <td>${item.quantity}</td>
                    
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
            
            <div class="row mt-3">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm">
                        <tr><td><strong>Total Amount:</strong></td><td><strong>Rp ${Number(transaction.total_price).toLocaleString('id-ID')}</strong></td></tr>
                    </table>
                </div>
            </div>
        `;
        
        document.getElementById('transactionDetailContent').innerHTML = html;
    }
    
    function printReceipt() {
        if (!currentTransactionData) {
            alert('No transaction data available');
            return;
        }
        
        const transaction = currentTransactionData.transaction;
        const items = currentTransactionData.items;
        const user = currentTransactionData.user;
        
        let printContent = `
            <html>
            <head>
                <title>Transaction Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .receipt-info { margin-bottom: 20px; }
                    .receipt-info table { width: 100%; }
                    .receipt-info td { padding: 5px; }
                    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .items-table th { background-color: #f2f2f2; }
                    .total { text-align: right; font-weight: bold; font-size: 18px; }
                    .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>BOOKSTORE RECEIPT</h2>
                    <p>Transaction Receipt</p>
                </div>
                
                <div class="receipt-info">
                    <table>
                        <tr>
                            <td><strong>Transaction ID:</strong></td>
                            <td>${transaction.id}</td>
                            <td><strong>Date:</strong></td>
                            <td>${transaction.created_at}</td>
                        </tr>
                        <tr>
                            <td><strong>Customer:</strong></td>
                            <td>${user.name}</td>
                            <td><strong>Email:</strong></td>
                            <td>${user.email}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>${transaction.status}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Qty</th>
                           
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        items.forEach(item => {
            printContent += `
                <tr>
                    <td>${item.title}</td>t
                    <td>${item.author}</td>
                    <td>Rp ${Number(item.book_price).toLocaleString('id-ID')}</td>
                    <td>${item.quantity}</td>
       
                </tr>
            `;
        });
        
        printContent += `
                    </tbody>
                </table>
                
                <div class="total">
                    <p>TOTAL: Rp ${Number(transaction.total_price).toLocaleString('id-ID')}</p>
                </div>
                
                <div class="footer">
                    <p>Thank you for your purchase!</p>
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                </div>
            </body>
            </html>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }
</script>
</body>
</html>
