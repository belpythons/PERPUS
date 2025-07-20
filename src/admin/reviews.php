<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Proses hapus review
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT r.*, b.title as book_title FROM reviews r JOIN books b ON r.book_id = b.id WHERE r.id = ?");
    $stmt->execute([$id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($review) {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        logAdminActivity($_SESSION['user_id'], 'Delete Review', "Deleted review for book: " . $review['book_title']);
    }
    header('Location: reviews.php');
    exit();
}

// Ambil semua review
$stmt = $pdo->query("SELECT r.*, u.name as user_name, b.title as book_title 
                     FROM reviews r 
                     JOIN users u ON r.user_id = u.id 
                     JOIN books b ON r.book_id = b.id 
                     ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Buku - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Review Buku</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Buku</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Komentar</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?= $review['id'] ?></td>
                                        <td><?= htmlspecialchars($review['book_title']) ?></td>
                                        <td><?= htmlspecialchars($review['user_name']) ?></td>
                                        <td>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            (<?= $review['rating'] ?>/5)
                                        </td>
                                        <td>
                                            <?php if (strlen($review['comment']) > 100): ?>
                                                <?= htmlspecialchars(substr($review['comment'], 0, 100)) ?>...
                                            <?php else: ?>
                                                <?= htmlspecialchars($review['comment']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDate($review['created_at']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewReview('<?= addslashes($review['comment']) ?>')">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <a href="reviews.php?delete=<?= $review['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal View Review -->
    <div class="modal fade" id="viewReviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="reviewContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewReview(comment) {
            document.getElementById('reviewContent').innerText = comment;
            new bootstrap.Modal(document.getElementById('viewReviewModal')).show();
        }
    </script>
</body>
</html>
