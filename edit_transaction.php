<?php
session_start();
include('includes/db_connect.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID transaksi dari URL
$id = $_GET['id'];
$user_id = $_SESSION['user']['id'];
$isAdmin = $_SESSION['user']['role'] === 'admin';

// Cek apakah admin mencoba mengedit transaksi pengguna lain
if ($isAdmin) {
    die("Admin tidak bisa mengedit transaksi pengguna lain.");
}

// Ambil data transaksi yang akan diedit (hanya transaksi milik user yang login)
$query = "SELECT * FROM transactions WHERE id = $id AND user_id = $user_id";
$result = $conn->query($query);
$data = $result->fetch_assoc();

// Cek apakah transaksi ditemukan dan milik user yang sedang login
if (!$data) {
    die("Transaksi tidak ditemukan atau Anda tidak memiliki akses.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];
    $description = $_POST['description'];

    // Update transaksi di database
    $update_query = "UPDATE transactions SET amount = '$amount', transaction_type = '$transaction_type', description = '$description' WHERE id = $id";
    if ($conn->query($update_query)) {
        header("Location: dashboard.php");  // Redirect ke dashboard setelah berhasil
        exit();
    } else {
        $error = "Terjadi kesalahan saat memperbarui transaksi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi - Aplikasi Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .card {
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }
        .card-body {
            padding: 2rem;
        }
        .card-title {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center">Edit Transaksi</h3>

            <!-- Tampilkan pesan error jika ada -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form edit transaksi -->
            <form method="POST">
                <div class="mb-3">
                    <label for="amount" class="form-label">Jumlah</label>
                    <input type="number" name="amount" id="amount" class="form-control" value="<?= htmlspecialchars($data['amount']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="transaction_type" class="form-label">Tipe Transaksi</label>
                    <select name="transaction_type" id="transaction_type" class="form-select" required>
                        <option value="in" <?= $data['transaction_type'] == 'in' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="out" <?= $data['transaction_type'] == 'out' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($data['description']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Transaksi</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
