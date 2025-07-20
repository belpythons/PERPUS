<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Ambil semua user activities
$stmt = $pdo->query("SELECT ua.*, u.name as user_name FROM user_activities ua 
                     JOIN users u ON ua.user_id = u.id 
                     ORDER BY ua.created_at DESC");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">User Activity</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Activity Type</th>
                                    <th>Description</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?= $activity['id'] ?></td>
                                        <td><?= htmlspecialchars($activity['user_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getActivityBadgeColor($activity['activity_type']) ?>">
                                                <?= htmlspecialchars($activity['activity_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (strlen($activity['description']) > 50): ?>
                                                <span title="<?= htmlspecialchars($activity['description']) ?>">
                                                    <?= htmlspecialchars(substr($activity['description'], 0, 50)) ?>...
                                                </span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($activity['description']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDate($activity['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function getActivityBadgeColor($type) {
    switch ($type) {
        case 'login':
            return 'success';
        case 'logout':
            return 'secondary';
        case 'purchase':
            return 'primary';
        case 'review':
            return 'info';
        default:
            return 'light';
    }
}
?>
