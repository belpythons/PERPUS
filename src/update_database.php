<?php
require_once 'config/database.php';

try {
    $pdo = getConnection();
    
    // Check if image column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM books LIKE 'image'");
    $column_exists = $stmt->rowCount() > 0;
    
    if (!$column_exists) {
        // Add image column to books table
        $sql = "ALTER TABLE books ADD COLUMN image VARCHAR(255) NULL AFTER stock";
        $pdo->exec($sql);
        echo "✅ Image column added successfully to books table!<br>";
    } else {
        echo "ℹ️ Image column already exists in books table.<br>";
    }
    
    // Note: Using URL-based images, no upload directory needed
    
    echo "<br>🎉 Database update completed successfully!<br>";
    echo "<a href='admin/books.php'>Go to Books Management</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
