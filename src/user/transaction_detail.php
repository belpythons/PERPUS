<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Debug: Log that we're starting
error_log("Transaction detail script started");

// Debug: Check session data
error_log("Session data: " . print_r($_SESSION, true));

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Debug: Log before database connection
error_log("Attempting to connect to database");

try {
    $pdo = getConnection();
    error_log("Database connection successful");
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Periksa apakah ID transaksi diberikan
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID is required']);
    exit();
}

$transaction_id = $_GET['id'];
error_log("Transaction ID: " . $transaction_id);

try {
    // Ambil detail transaksi (hanya milik user yang login)
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['error' => 'Transaction not found']);
        exit();
    }
    
    // Ambil detail items transaksi
    $stmt = $pdo->prepare("
        SELECT td.*, b.title, b.author, b.price as book_price 
        FROM transaction_details td 
        JOIN books b ON td.book_id = b.id 
        WHERE td.transaction_id = ?
    ");
    $stmt->execute([$transaction_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Ambil informasi user
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$transaction['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Gabungkan data
    $response = [
        'transaction' => $transaction,
        'items' => $items,
        'user' => $user
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Exception caught: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
