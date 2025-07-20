<?php
require_once 'config/database.php';

try {
    $pdo = getConnection();
    
    // Update books with valid image URLs
    $updates = [
        1 => 'https://via.placeholder.com/150x200/4285F4/FFFFFF?text=Laravel',
        2 => 'https://via.placeholder.com/150x200/EA4335/FFFFFF?text=Inspiratif', 
        3 => 'https://via.placeholder.com/150x200/34A853/FFFFFF?text=Steve+Jobs'
    ];
    
    echo "<h3>🔄 Updating Book Images</h3>";
    
    foreach ($updates as $bookId => $imageUrl) {
        $stmt = $pdo->prepare("UPDATE books SET image = ? WHERE id = ?");
        if ($stmt->execute([$imageUrl, $bookId])) {
            echo "<p>✅ Book ID $bookId updated with image: $imageUrl</p>";
        } else {
            echo "<p>❌ Failed to update Book ID $bookId</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>📚 Updated Books Data</h3>";
    
    // Show updated data
    $books = $pdo->query("SELECT id, title, image FROM books ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Image URL</th><th>Image Preview</th></tr>";
    
    foreach ($books as $book) {
        echo "<tr>";
        echo "<td>" . $book['id'] . "</td>";
        echo "<td>" . htmlspecialchars($book['title']) . "</td>";
        echo "<td>" . htmlspecialchars($book['image']) . "</td>";
        echo "<td>";
        if (!empty($book['image'])) {
            echo '<img src="' . htmlspecialchars($book['image']) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" onerror="this.src=\'data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;50&quot; height=&quot;50&quot; viewBox=&quot;0 0 50 50&quot;><rect width=&quot;50&quot; height=&quot;50&quot; fill=&quot;%23f8f9fa&quot;/><text x=&quot;25&quot; y=&quot;25&quot; text-anchor=&quot;middle&quot; dy=&quot;.35em&quot; fill=&quot;%236c757d&quot; font-size=&quot;10&quot;>No Image</text></svg>\'; this.onerror=null;">';
        } else {
            echo "<span class='text-muted'>No Image</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p><strong>✅ All books updated successfully!</strong></p>";
    echo "<p><a href='admin/books.php'>← Back to Books Management</a></p>";
    
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
