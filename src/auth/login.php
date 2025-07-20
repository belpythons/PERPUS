<?php
/*
 * ========================================================================
 * FILE: login.php
 * DESKRIPSI: Halaman dan proses login untuk user dan admin
 * FUNGSI:
 *   - Menampilkan form login dengan Bootstrap styling
 *   - Memproses authentication user/admin
 *   - Redirect ke dashboard sesuai role setelah login berhasil
 *   - Log aktivitas login untuk audit trail
 *   - Menampilkan demo account untuk testing
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * ========================================================================
 */

// Memulai session untuk menyimpan data login user
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// ========================================================================
// SECTION: Login Process Handler
// ========================================================================
// Memproses form login ketika user submit (method POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form login
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Koneksi ke database
    $pdo = getConnection();
    
    // Cari user berdasarkan email yang diinput
    // Menggunakan prepared statement untuk keamanan (mencegah SQL injection)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifikasi user dan password
    // password_verify() digunakan untuk membandingkan password plain text dengan hash
    if ($user && password_verify($password, $user['password'])) {
        // Login berhasil - Set session variables
        $_SESSION['user_id'] = $user['id'];       // ID user untuk referensi database
        $_SESSION['user_name'] = $user['name'];   // Nama user untuk ditampilkan
        $_SESSION['user_role'] = $user['role'];   // Role untuk authorization (admin/user)
        
        // Log aktivitas login untuk audit trail dan monitoring
        logUserActivity($user['id'], 'login', 'User logged in');
        
        // Redirect user ke dashboard sesuai dengan role mereka
        if ($user['role'] === 'admin') {
            header('Location: ../admin/dashboard.php');  // Admin ke admin dashboard
        } else {
            header('Location: ../user/index.php');       // User biasa ke user dashboard
        }
        exit(); // Stop eksekusi script setelah redirect
    } else {
        // Login gagal - set error message untuk ditampilkan ke user
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow mt-5">
                    <div class="card-header text-center bg-primary text-white">
                        <h4><i class="fas fa-book"></i> Bookstore Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Demo Account:</strong><br>
                                Admin: admin@bookstore.com / password<br>
                                User: user@bookstore.com / password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
