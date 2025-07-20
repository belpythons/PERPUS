<?php
/*
 * ========================================================================
 * FILE: admin/dashboard.php
 * DESKRIPSI: Dashboard utama untuk administrator sistem
 * FUNGSI:
 *   - Menampilkan overview statistik sistem (users, books, transactions, categories)
 *   - Menyediakan navigasi ke berbagai modul admin
 *   - Menampilkan welcome message untuk admin yang login
 *   - Interface utama untuk manajemen sistem bookstore
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: Admin Only
 * ========================================================================
 */

// Memulai session untuk mengecek status login admin
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// ========================================================================
// SECTION: Admin Access Control
// ========================================================================
// Validasi akses - hanya admin yang boleh mengakses halaman ini
// Jika bukan admin atau belum login, redirect ke halaman login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit(); // Stop eksekusi untuk keamanan
}

// ========================================================================
// SECTION: Dashboard Statistics Data Fetching
// ========================================================================
// Mendapatkan koneksi database
$pdo = getConnection();

// Ambil berbagai statistik untuk ditampilkan di dashboard cards
// Menggunakan COUNT(*) untuk menghitung total record di setiap tabel

// Total semua user (termasuk admin dan user biasa)
$jumlahUser = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Total buku yang tersedia dalam sistem
$jumlahBuku = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();

// Total transaksi yang pernah terjadi
$jumlahTransaksi = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();

// Total kategori buku yang tersedia
$jumlahKategori = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #333;
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #333;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="text-muted">
                        Welcome, <?= $_SESSION['user_name'] ?>!
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="card stat-card primary mb-3">
                            <div class="card-body text-center">
                                <h3 class="card-title"><?=$jumlahUser?></h3>
                                <p class="card-text">Total Users</p>
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card success mb-3">
                            <div class="card-body text-center">
                                <h3 class="card-title"><?=$jumlahBuku?></h3>
                                <p class="card-text">Total Books</p>
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card danger mb-3">
                            <div class="card-body text-center">
                                <h3 class="card-title"><?=$jumlahTransaksi?></h3>
                                <p class="card-text">Total Transactions</p>
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card warning mb-3">
                            <div class="card-body text-center">
                                <h3 class="card-title"><?=$jumlahKategori?></h3>
                                <p class="card-text">Total Categories</p>
                                <i class="fas fa-tags fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

