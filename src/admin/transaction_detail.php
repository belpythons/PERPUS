<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Periksa parameter transaction_id
if (!isset($_GET['transaction_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing transaction_id parameter']);
    exit();
}

$transaction_id = $_GET['transaction_id'];
$pdo = getConnection();

try {
    // Ambil data transaksi utama
    $stmt = $pdo->prepare("SELECT t.*, u.name as user_name, u.email as user_email 
                           FROM transactions t 
                           JOIN users u ON t.user_id = u.id 
                           WHERE t.id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['error' => 'Transaction not found']);
        exit();
    }
    
    // Ambil detail transaksi dengan informasi buku
    $stmt = $pdo->prepare("SELECT td.*, b.title as book_title, b.author as book_author, b.price as book_price
                           FROM transaction_details td
                           JOIN books b ON td.book_id = b.id
                           WHERE td.transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $transaction_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'transaction' => $transaction,
        'details' => $transaction_details
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
