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
</style>

<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h4 class="text-white"><i class="fas fa-book-open"></i> Bookstore</h4>
            <p class="text-white-50">Admin Panel</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" href="users.php">
                    <i class="fas fa-users me-2"></i> Manajemen Akun
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>" href="categories.php">
                    <i class="fas fa-tags me-2"></i> Manajemen Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'books.php' ? 'active' : '' ?>" href="books.php">
                    <i class="fas fa-book me-2"></i> Manajemen Buku
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : '' ?>" href="reviews.php">
                    <i class="fas fa-star me-2"></i> Review Buku
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'active' : '' ?>" href="transactions.php">
                    <i class="fas fa-shopping-cart me-2"></i> Manajemen Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'activity_logs.php' ? 'active' : '' ?>" href="activity_logs.php">
                    <i class="fas fa-history me-2"></i> Activity Log
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'user_activities.php' ? 'active' : '' ?>" href="user_activities.php">
                    <i class="fas fa-user-clock me-2"></i> User Activity
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
