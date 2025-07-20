<?php
/*
 * ========================================================================
 * FILE: admin/books.php
 * DESKRIPSI: Halaman manajemen buku untuk administrator
 * FUNGSI:
 *   - CRUD operations untuk buku (Create, Read, Update, Delete)
 *   - Menampilkan daftar semua buku dalam tabel dengan informasi lengkap
 *   - Modal form untuk menambah dan mengedit buku
 *   - Upload dan manajemen gambar buku (URL-based)
 *   - Integrasi dengan kategori buku
 *   - Logging aktivitas admin untuk audit trail
 *   - Konfirmasi sebelum menghapus buku
 * AUTHOR: [Nama Developer]
 * CREATED: [Tanggal]
 * AKSES: Admin Only
 * ========================================================================
 */

// Memulai session untuk mengecek status login admin
session_start();

// Include konfigurasi database dan helper functions
require_once '../config/database.php';

// ========================================================================
// SECTION: Admin Access Control
// ========================================================================
// Validasi akses - hanya admin yang boleh mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit(); // Stop eksekusi untuk keamanan
}

// Mendapatkan koneksi database
$pdo = getConnection();

// ========================================================================
// SECTION: Add New Book Process Handler
// ========================================================================
// Memproses form tambah buku baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    // Mengambil dan membersihkan data dari form
    $title = trim($_POST['title']);              // Judul buku
    $author = trim($_POST['author']);            // Nama penulis
    $category_id = $_POST['category_id'];        // ID kategori buku
    $description = trim($_POST['description']);  // Deskripsi/sinopsis buku
    $price = floatval($_POST['price']);          // Harga buku dalam rupiah
    $stock = intval($_POST['stock']);            // Jumlah stok buku
    $image = !empty($_POST['image']) ? trim($_POST['image']) : null; // URL gambar buku (optional)
    
    // Insert data buku baru ke database
    $stmt = $pdo->prepare("INSERT INTO books (title, author, category_id, description, price, stock, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt->execute([$title, $author, $category_id, $description, $price, $stock, $image])) {
        // Log aktivitas admin untuk audit trail
        logAdminActivity($_SESSION['user_id'], 'Create Book', "Added book: $title");
        $success = "Buku berhasil ditambahkan!"; // Pesan sukses untuk ditampilkan ke user
    } else {
        $error = "Gagal menambahkan buku! Silakan coba lagi."; // Pesan error
    }
}

// ========================================================================
// SECTION: Edit Book Process Handler
// ========================================================================
// Memproses form edit buku yang sudah ada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_book'])) {
    // Mengambil data dari form edit
    $id = intval($_POST['book_id']);             // ID buku yang akan diedit
    $title = trim($_POST['title']);              // Judul buku baru
    $author = trim($_POST['author']);            // Nama penulis baru
    $category_id = $_POST['category_id'];        // ID kategori buku baru
    $description = trim($_POST['description']);  // Deskripsi baru
    $price = floatval($_POST['price']);          // Harga baru
    $stock = intval($_POST['stock']);            // Stok baru
    
    // Handle update gambar - jika ada input baru gunakan yang baru, jika tidak gunakan yang lama
    $image = !empty($_POST['image']) ? trim($_POST['image']) : (isset($_POST['current_image']) ? $_POST['current_image'] : null);

    // Update data buku di database
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category_id = ?, description = ?, price = ?, stock = ?, image = ?, updated_at = NOW() WHERE id = ?");
    
    if ($stmt->execute([$title, $author, $category_id, $description, $price, $stock, $image, $id])) {
        // Log aktivitas admin untuk audit trail
        logAdminActivity($_SESSION['user_id'], 'Edit Book', "Updated book: $title");
        $success = "Buku berhasil diperbarui!"; // Pesan sukses
    } else {
        $error = "Gagal memperbarui buku! Silakan coba lagi."; // Pesan error
    }
}

// ========================================================================
// SECTION: Delete Book Process Handler
// ========================================================================
// Memproses penghapusan buku berdasarkan parameter GET
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // ID buku yang akan dihapus
    
    // Ambil judul buku terlebih dahulu untuk logging
    $stmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($book) {
        // Hapus buku dari database
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log aktivitas admin untuk audit trail
        logAdminActivity($_SESSION['user_id'], 'Delete Book', "Deleted book: " . $book['title']);
    }
    
    // Redirect kembali ke halaman books untuk refresh data
    header('Location: books.php');
    exit();
}

// ========================================================================
// SECTION: Data Fetching for Display
// ========================================================================
// Ambil semua buku dengan JOIN ke tabel categories untuk menampilkan nama kategori
// ORDER BY created_at DESC untuk menampilkan buku terbaru terlebih dahulu
$books = $pdo->query("
    SELECT b.*, c.name as category_name 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    ORDER BY b.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua kategori untuk dropdown di form tambah/edit buku
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manajemen Buku</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        <i class="fas fa-plus"></i> Tambah Buku
                    </button>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?= $book['id'] ?></td>
                                        <td>
                                            <?php if ($book['image']): ?>
                                                <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">

                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($book['title']) ?></td>
                                        <td><?= htmlspecialchars($book['author']) ?></td>
                                        <td><?= htmlspecialchars($book['category_name']) ?></td>
                                        <td><?= formatRupiah($book['price']) ?></td>
                                        <td><?= $book['stock'] ?></td>
                                        <td><?= formatDate($book['created_at']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editBook(<?= $book['id'] ?>, '<?= addslashes($book['title']) ?>', '<?= addslashes($book['author']) ?>', <?= $book['category_id'] ?>, '<?= addslashes($book['description']) ?>', <?= $book['price'] ?>, <?= $book['stock'] ?>, '<?= $book['image'] ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="books.php?delete=<?= $book['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
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

    <!-- Modal Tambah Buku -->
    <div class="modal fade" id="addBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Penulis</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">URL Gambar Buku</label>
                            <input type="url" class="form-control" id="image" name="image" placeholder="https://example.com/image.jpg">
                            <div class="form-text">Masukkan URL gambar untuk buku ini (opsional).</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_book" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Buku -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_book_id" name="book_id">
                        <input type="hidden" id="edit_current_image" name="current_image">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_author" class="form-label">Penulis</label>
                            <input type="text" class="form-control" id="edit_author" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_stock" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="edit_stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">URL Gambar Buku</label>
                            <div id="current_image_preview" class="mb-2"></div>
                            <input type="url" class="form-control" id="edit_image" name="image" placeholder="https://example.com/image.jpg">
                            <div class="form-text">Masukkan URL gambar untuk buku ini (opsional).</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_book" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editBook(id, title, author, category_id, description, price, stock, image) {
            document.getElementById('edit_book_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_category_id').value = category_id;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_current_image').value = image;
            document.getElementById('edit_image').value = image; // Set current image URL in the input
            
            // Show current image preview
            const imagePreview = document.getElementById('current_image_preview');
            if (image) {
                imagePreview.innerHTML = `
                    <div class="text-muted mb-2">Gambar saat ini:</div>
                    <img src="${image}" alt="${title}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;" onerror="this.src='data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\" viewBox=\"0 0 100 100\"><rect width=\"100\" height=\"100\" fill=\"%23f8f9fa\"/><text x=\"50\" y=\"50\" text-anchor=\"middle\" dy=\".35em\" fill=\"%236c757d\" font-size=\"12\">No Image</text></svg>'">
                `;
            } else {
                imagePreview.innerHTML = '<div class="text-muted mb-2">Belum ada gambar</div>';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editBookModal'));
            modal.show();
        }
    </script>
</body>
</html>
