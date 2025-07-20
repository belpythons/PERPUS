<?php
/*
 * ========================================================================
 * FILE: logout.php
 * DESKRIPSI: Halaman logout untuk mengakhiri session user/admin
 * FUNGSI:
 *   - Melakukan log aktivitas logout sebelum mengakhiri session
 *   - Menghancurkan session untuk keamanan
 *   - Redirect user ke halaman login setelah logout berhasil
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: User yang sudah login (admin atau user biasa)
 * ========================================================================
 */

// Memulai session untuk mengakses data session yang aktif
session_start();

// Include konfigurasi database dan helper functions untuk logging
require_once '../config/database.php';

// ========================================================================
// SECTION: Logout Activity Logging
// ========================================================================
// Log aktivitas logout jika user sedang login (untuk audit trail)
// Ini penting untuk melacak kapan user terakhir kali logout dari sistem
if (isset($_SESSION['user_id'])) {
    logUserActivity($_SESSION['user_id'], 'logout', 'User logged out successfully');
}

// ========================================================================
// SECTION: Session Cleanup & Redirect
// ========================================================================
// Hancurkan session secara permanen dari server untuk keamanan
// session_destroy() menghapus semua data session
session_destroy();

// Redirect user ke halaman login setelah logout berhasil
header('Location: login.php');
exit(); // Stop eksekusi script untuk keamanan
?>
