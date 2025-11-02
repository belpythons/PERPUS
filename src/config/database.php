<?php
/*
 * ========================================================================
 * FILE: database.php
 * DESKRIPSI: Konfigurasi database dan fungsi-fungsi helper untuk aplikasi
 * FUNGSI:
 *   - Mengatur koneksi ke database MySQL menggunakan PDO
 *   - Menyediakan fungsi helper untuk logging aktivitas
 *   - Menyediakan fungsi utility untuk format data
 *   - Menyediakan fungsi untuk validasi role user
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * ========================================================================
 */

// ========================================================================
// SECTION: Database Configuration Constants
// ========================================================================
// Konfigurasi koneksi database - ubah sesuai dengan setup environment Anda
define('DB_HOST', 'localhost');     // Host database (biasanya localhost untuk development)
define('DB_NAME', 'vigilant_increase_roy_bookstore_db');  // Nama database yang digunakan
define('DB_USER', 'root');          // Username database (default XAMPP adalah 'root')
define('DB_PASS', '');              // Password database (default XAMPP kosong)

// ========================================================================
// SECTION: Database Connection Function
// ========================================================================
/**
 * Membuat dan mengembalikan koneksi PDO ke database
 * 
 * @return PDO Object koneksi database
 * @throws PDOException Jika koneksi gagal
 * 
 * Fungsi ini menggunakan PDO (PHP Data Objects) untuk koneksi yang lebih aman
 * dan mendukung prepared statements untuk mencegah SQL injection
 */
function getConnection() {
    try {
        // Membuat koneksi PDO dengan parameter yang telah didefinisikan
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        
        // Set mode error menjadi exception agar error mudah di-handle
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Set charset ke UTF-8 untuk mendukung karakter internasional
        $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
        
        return $pdo;
    } catch(PDOException $e) {
        // Jika koneksi gagal, tampilkan error dan hentikan eksekusi
        die("Connection failed: " . $e->getMessage());
    }
}

// ========================================================================
// SECTION: Activity Logging Functions
// ========================================================================

/**
 * Log aktivitas yang dilakukan oleh admin untuk audit trail
 * 
 * @param int $admin_id ID admin yang melakukan aktivitas
 * @param string $action Jenis aktivitas yang dilakukan (create, update, delete, dll)
 * @param string $details Detail tambahan tentang aktivitas (optional)
 * 
 * Fungsi ini mencatat semua aktivitas admin untuk keperluan audit dan monitoring
 */
function logAdminActivity($admin_id, $action, $details = '') {
    $pdo = getConnection();
    
    // Prepared statement untuk keamanan
    $stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$admin_id, $action, $details]);
}

/**
 * Log aktivitas yang dilakukan oleh user biasa
 * 
 * @param int $user_id ID user yang melakukan aktivitas
 * @param string $activity_type Jenis aktivitas (login, logout, borrow_book, dll)
 * @param string $description Deskripsi detail aktivitas (optional)
 * 
 * Fungsi ini mencatat aktivitas user untuk analisis usage pattern dan troubleshooting
 */
function logUserActivity($user_id, $activity_type, $description = '') {
    $pdo = getConnection();
    
    // Prepared statement untuk keamanan
    $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity_type, description, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $activity_type, $description]);
}

// ========================================================================
// SECTION: User Validation Functions
// ========================================================================

/**
 * Mengecek apakah user memiliki role admin
 * 
 * @param int $user_id ID user yang akan dicek
 * @return bool True jika user adalah admin, false jika bukan
 * 
 * Fungsi ini digunakan untuk authorization dan access control
 */
function isAdmin($user_id) {
    $pdo = getConnection();
    
    // Query untuk mengambil role user berdasarkan ID
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return true jika user ada dan role-nya adalah 'admin'
    return $user && $user['role'] === 'admin';
}

// ========================================================================
// SECTION: Utility/Helper Functions
// ========================================================================

/**
 * Format angka menjadi format mata uang Rupiah Indonesia
 * 
 * @param float $amount Jumlah uang yang akan diformat
 * @return string Format rupiah (contoh: "Rp 150.000")
 * 
 * Fungsi ini mengubah angka biasa menjadi format yang user-friendly
 * dengan pemisah ribuan menggunakan titik sesuai format Indonesia
 */
function formatRupiah($amount) {
    return "Rp " . number_format($amount, 0, ',', '.');
}

/**
 * Format tanggal dari database menjadi format yang lebih mudah dibaca
 * 
 * @param string $date Tanggal dalam format database (YYYY-MM-DD HH:MM:SS)
 * @return string Tanggal dalam format Indonesia (DD-MM-YYYY HH:MM)
 * 
 * Fungsi ini mengubah format tanggal database menjadi format yang familiar
 * untuk user Indonesia
 */
function formatDate($date) {
    return date('d-m-Y H:i', strtotime($date));
}
?>
