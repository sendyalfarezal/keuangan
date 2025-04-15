<?php
session_start();
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user']['id'];  // Ambil ID pengguna yang sedang login

    // Insert transaksi ke database
    $query = "INSERT INTO transactions (user_id, amount, transaction_type, description) 
              VALUES ('$user_id', '$amount', '$transaction_type', '$description')";
    if ($conn->query($query)) {
        header("Location: dashboard.php");  // Redirect ke dashboard setelah berhasil
        exit();
    } else {
        $error = "Terjadi kesalahan saat menambahkan transaksi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi - Aplikasi Keuangan</title>
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
        .alert {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center">Tambah Transaksi</h3>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="amount" class="form-label">Jumlah</label>
                    <input type="number" name="amount" id="amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="transaction_type" class="form-label">Tipe Transaksi</label>
                    <select name="transaction_type" id="transaction_type" class="form-select" required>
                        <option value="in">Pemasukan</option>
                        <option value="out">Pengeluaran</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Tambah Transaksi</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
