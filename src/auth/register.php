<?php
/*
 * ========================================================================
 * FILE: register.php
 * DESKRIPSI: Halaman pendaftaran user baru untuk sistem bookstore
 * FUNGSI:
 *   - Menampilkan form registrasi dengan validasi client-side dan server-side
 *   - Memvalidasi password confirmation untuk memastikan user tidak salah ketik
 *   - Mengecek duplikasi email sebelum insert data user baru
 *   - Hash password untuk keamanan menggunakan PASSWORD_DEFAULT
 *   - Set default role sebagai 'user' untuk pendaftaran umum
 *   - Memberikan feedback success/error kepada user
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: Public (tidak perlu login)
 * ========================================================================
 */

// Memulai session untuk menyimpan feedback message
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// ========================================================================
// SECTION: Registration Process Handler
// ========================================================================
// Memproses form registrasi ketika user submit (method POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form registrasi
    $name = trim($_POST['name']);                     // Nama lengkap user
    $email = trim($_POST['email']);                   // Email user (harus unik)
    $password = $_POST['password'];                   // Password yang diinput user
    $confirm_password = $_POST['confirm_password'];   // Konfirmasi password
    
    // ========================================================================
    // SECTION: Password Validation
    // ========================================================================
    // Validasi apakah password dan konfirmasi password cocok
    if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok! Pastikan kedua password sama.";
    } else {
        // Password cocok, lanjutkan ke proses validasi database
        $pdo = getConnection();
        
        // ========================================================================
        // SECTION: Email Duplication Check
        // ========================================================================
        // Cek apakah email sudah terdaftar di database
        // Menggunakan prepared statement untuk keamanan
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // Email sudah ada - tampilkan error
            $error = "Email sudah terdaftar! Gunakan email lain atau login jika sudah memiliki akun.";
        } else {
            // ========================================================================
            // SECTION: New User Registration
            // ========================================================================
            // Email belum terdaftar - lanjutkan pendaftaran
            // Hash password untuk keamanan sebelum disimpan ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru dengan role default 'user'
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                // Registrasi berhasil - tampilkan pesan sukses
                $success = "Registrasi berhasil! Silakan login menggunakan email dan password Anda.";
            } else {
                // Terjadi error saat insert - tampilkan pesan error
                $error = "Gagal melakukan registrasi! Silakan coba lagi atau hubungi administrator.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow mt-5">
                    <div class="card-header text-center bg-success text-white">
                        <h4><i class="fas fa-user-plus"></i> Register</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Daftar</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
