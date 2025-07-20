<?php
session_start();
require_once '../config/database.php';

// Periksa apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$pdo = getConnection();

// Proses ubah status transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['transaction_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    logAdminActivity($_SESSION['user_id'], 'Update Transaction', "Updated transaction #$id to $status");
}

// Ambil total revenue dari transaksi completed
$stmt = $pdo->query("SELECT SUM(total_price) as total_revenue FROM transactions WHERE status = 'completed'");
$totalRevenue = $stmt->fetchColumn();
if (!$totalRevenue) $totalRevenue = 0;

// Ambil semua transaksi
$stmt = $pdo->query("SELECT t.*, u.name as user_name FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .revenue-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .revenue-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
        }
        .revenue-icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        .revenue-amount {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        /* Enhanced Print Styles */
        @media print {
            /* Hide all non-essential elements */
            body * { visibility: hidden; }
            
            /* Show only the print-specific content */
            .print-area, .print-area * { visibility: visible; }
            .print-revenue, .print-revenue * { visibility: visible; }
            .print-table, .print-table * { visibility: visible; }
            
            /* Position print content */
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            
            /* Hide sidebar and other UI elements */
            .sidebar, .no-print { display: none !important; }
            
            /* Clean up cards for print */
            .card { 
                border: 1px solid #dee2e6 !important; 
                box-shadow: none !important; 
                page-break-inside: avoid;
            }
            
            /* Revenue card print styling */
            .revenue-card {
                background: #f8f9fa !important;
                color: #333 !important;
                border: 2px solid #6c757d !important;
                margin-bottom: 20px;
            }
            
            /* Table print styling */
            .table {
                font-size: 12px;
            }
            
            .table th {
                background-color: #f8f9fa !important;
                border-bottom: 2px solid #333 !important;
            }
            
            /* Hide action column in print */
            .table th:last-child,
            .table td:last-child {
                display: none !important;
            }
            
            /* Print header */
            .print-header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            
            .print-date {
                text-align: right;
                margin-bottom: 20px;
                font-size: 12px;
                color: #666;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manajemen Transaksi</h1>
                </div>

                <!-- Print Area - Hidden on screen, visible on print -->
                <div class="print-area d-none">
                    <div class="print-header">
                        <h1>LAPORAN TRANSAKSI BOOKSTORE</h1>
                        <div class="print-date">Dicetak pada: <?= date('d F Y, H:i:s') ?></div>
                    </div>
                    
                    <!-- Revenue Summary for Print -->
                    <div class="print-revenue mb-4">
                        <div class="card revenue-card">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h4 class="mb-3">Total Revenue (Completed Transactions)</h4>
                                    <h2 class="revenue-amount"><?= formatRupiah($totalRevenue) ?></h2>
                                    <p class="mb-0">Total dari semua transaksi yang telah selesai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Table for Print -->
                    <div class="print-table">
                        <h4 class="mb-3">Daftar Transaksi</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?= $transaction['id'] ?></td>
                                        <td><?= htmlspecialchars($transaction['user_name']) ?></td>
                                        <td><?= formatRupiah($transaction['total_price']) ?></td>
                                        <td><?= htmlspecialchars($transaction['status']) ?></td>
                                        <td><?= formatDate($transaction['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Screen Content - Hidden on print -->
                <div class="screen-content">
                    <!-- Revenue Summary Card -->
                    <div class="row mb-4 no-print">
                        <div class="col-md-8">
                            <div class="card revenue-card">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave revenue-icon"></i>
                                        </div>
                                        <div class="col">
                                            <h5 class="card-title mb-1">Total Revenue (Completed Transactions)</h5>
                                            <p class="revenue-amount"><?= formatRupiah($totalRevenue) ?></p>
                                            <small class="opacity-75">Total dari semua transaksi yang telah selesai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <button class="btn btn-outline-primary btn-lg w-100" onclick="printReport()">
                                <i class="fas fa-print me-2"></i>
                                Print Report
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?= $transaction['id'] ?></td>
                                            <td><?= htmlspecialchars($transaction['user_name']) ?></td>
                                            <td><?= formatRupiah($transaction['total_price']) ?></td>
                                            <td><?= htmlspecialchars($transaction['status']) ?></td>
                                            <td><?= formatDate($transaction['created_at']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewTransactionDetail(<?= $transaction['id'] ?>)">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="updateTransactionStatus(<?= $transaction['id'] ?>, '<?= $transaction['status'] ?>')">
                                                    <i class="fas fa-edit"></i> Ubah Status
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Ubah Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="transaction_id" id="transactionId">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div class="modal fade" id="transactionDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetailModalBody">
                    <!-- Detail transaksi akan dimuat di sini -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateTransactionStatus(id, currentStatus) {
            document.getElementById('transactionId').value = id;
            document.getElementById('status').value = currentStatus;
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }

        function viewTransactionDetail(id) {
            fetch(`transaction_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const modalBody = document.getElementById('transactionDetailModalBody');
                    const transaction = data.transaction;
                    const details = data.details;

                    modalBody.innerHTML = `
                        <p><strong>ID Transaksi:</strong> ${transaction.id}</p>
                        <p><strong>Nama User:</strong> ${transaction.user_name}</p>
                        <p><strong>Total Harga:</strong> ${transaction.total_price}</p>
                        <p><strong>Status:</strong> ${transaction.status}</p>
                        <p><strong>Tanggal:</strong> ${transaction.created_at}</p>
                        <p><strong>Detail Barang:</strong></p>
                        <ul>
                            ${details.map(item => `<li>${item.book_title} - ${item.quantity} x ${item.price}</li>`).join('')}
                        </ul>
                    `;

                    // Show the modal
                    new bootstrap.Modal(document.getElementById('transactionDetailModal')).show();
                })
                .catch(error => console.error('Error fetching transaction details:', error));
        }

        function printReport() {
            // Show print area, hide screen content
            document.querySelector('.print-area').classList.remove('d-none');
            document.querySelector('.screen-content').style.display = 'none';
            
            // Trigger print
            window.print();
            
            // After print (when dialog closes), restore original view
            setTimeout(() => {
                document.querySelector('.print-area').classList.add('d-none');
                document.querySelector('.screen-content').style.display = 'block';
            }, 100);
        }
    </script>
</body>
</html>
