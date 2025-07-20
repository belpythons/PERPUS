<?php
require_once 'config/database.php';

try {
    $pdo = getConnection();
    
    // Check if image column exists
    echo "<h3>🔍 Check Database Structure</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM books");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check books data
    echo "<h3>📚 Books Data</h3>";
    $books = $pdo->query("SELECT id, title, image FROM books ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($books)) {
        echo "<p>❌ No books found in database!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image URL</th><th>Image Status</th></tr>";
        foreach ($books as $book) {
            echo "<tr>";
            echo "<td>" . $book['id'] . "</td>";
            echo "<td>" . htmlspecialchars($book['title']) . "</td>";
            echo "<td>" . htmlspecialchars($book['image']) . "</td>";
            echo "<td>";
            if (empty($book['image'])) {
                echo "❌ Empty/NULL";
            } else {
                echo "✅ Has URL";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test image display
    echo "<h3>🖼️ Image Display Test</h3>";
    foreach ($books as $book) {
        if (!empty($book['image'])) {
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
            echo "<p><strong>Book ID " . $book['id'] . ":</strong> " . htmlspecialchars($book['title']) . "</p>";
            echo "<p><strong>URL:</strong> " . htmlspecialchars($book['image']) . "</p>";
            echo "<img src='" . htmlspecialchars($book['image']) . "' alt='" . htmlspecialchars($book['title']) . "' style='width: 50px; height: 50px; object-fit: cover; border-radius: 5px;' onerror=\"this.src='data:image/svg+xml,<svg xmlns=\\\"http://www.w3.org/2000/svg\\\" width=\\\"50\\\" height=\\\"50\\\" viewBox=\\\"0 0 50 50\\\"><rect width=\\\"50\\\" height=\\\"50\\\" fill=\\\"%23f8f9fa\\\"/><text x=\\\"25\\\" y=\\\"25\\\" text-anchor=\\\"middle\\\" dy=\\\".35em\\\" fill=\\\"%236c757d\\\" font-size=\\\"10\\\">No Image</text></svg>'; this.onerror=null;\">";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>

<p><a href="admin/books.php">← Back to Books Management</a></p>
