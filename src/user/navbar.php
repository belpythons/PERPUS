<!-- CSS for Modern Navbar Styling -->
<style>
    :root {
        --primary-color: #667eea;
        --secondary-color: #764ba2;
        --accent-color: #e74c3c;
        --light-bg: #f8f9fa;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --card-hover-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
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
    
    .navbar-nav .dropdown-toggle {
        font-weight: 500;
        color: #2c3e50 !important;
        margin: 0 0.5rem;
        padding: 0.7rem 1.2rem !important;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .navbar-nav .dropdown-toggle:hover {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white !important;
        transform: translateY(-2px);
    }
    
    .dropdown-menu {
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        border: none;
        padding: 0.5rem 0;
    }
    
    .dropdown-item {
        padding: 0.7rem 1.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .navbar-nav .nav-link {
            margin: 0.25rem 0;
        }
    }
</style>

<!-- Modern Light Navbar -->
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
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'all_books.php' ? 'active' : '' ?>" href="all_books.php">
                        <i class="fas fa-book me-1"></i> Browse Books
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>" href="categories.php">
                        <i class="fas fa-tags me-1"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>" href="history.php">
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
