<?php
session_start();
include('includes/db_connect.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$isAdmin = $user['role'] === 'admin';

// Ambil data transaksi
$query = $isAdmin
    ? "SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC"
    : "SELECT * FROM transactions WHERE user_id = {$user['id']} ORDER BY created_at DESC";

$result = $conn->query($query);

// Hitung saldo masuk dan keluar
$inQuery = $isAdmin
    ? "SELECT SUM(amount) as total_in FROM transactions WHERE transaction_type='in'"
    : "SELECT SUM(amount) as total_in FROM transactions WHERE user_id = {$user['id']} AND transaction_type='in'";

$outQuery = $isAdmin
    ? "SELECT SUM(amount) as total_out FROM transactions WHERE transaction_type='out'"
    : "SELECT SUM(amount) as total_out FROM transactions WHERE user_id = {$user['id']} AND transaction_type='out'";

$in = $conn->query($inQuery)->fetch_assoc()['total_in'] ?? 0;
$out = $conn->query($outQuery)->fetch_assoc()['total_out'] ?? 0;
$saldo = $in - $out;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Keuangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4caf50, #2196f3);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .navbar {
            background-color: #6200ea;
        }

        .navbar-brand img {
            max-height: 35px;
        }

        .navbar-text {
            font-weight: 500;
        }

        .card, .table-container {
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-top: 20px;
        }

        .saldo-section {
            width: 32%;
        }

        .transactions-section {
            width: 65%;
        }

        .top-card {
            padding: 25px;
            border-radius: 15px;
            color: #fff;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .top-card::after {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 120%;
            height: 120%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(25deg);
            z-index: 0;
        }

        .top-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            z-index: 1;
            position: relative;
        }

        .top-card h4, .top-card p {
            z-index: 1;
            position: relative;
        }

        .table-container h3 {
            font-weight: 600;
            color: #6200ea;
        }

        .footer {
            background-color: #6200ea;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        h2 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .btn-success {
            background-color: #4caf50;
            border: none;
        }

        .btn-success:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .transactions-section, .saldo-section {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="logo.png" alt="Logo"> Aplikasi Keuangan
        </a>
        <div class="d-flex">
            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($user['username']); ?>
                <span class="badge <?= $isAdmin ? 'bg-warning text-dark' : 'bg-info' ?>">
                    <?= strtoupper($user['role']); ?>
                </span>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4">Dashboard <?= $isAdmin ? 'Admin' : 'User' ?></h2>
    <p class="text-center text-muted">Anda login sebagai <strong><?= $user['username']; ?></strong> (<?= strtoupper($user['role']); ?>)</p>

    <div class="dashboard-container">
        <!-- Tabel Transaksi -->
        <div class="transactions-section">
            <div class="table-container p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Daftar Transaksi</h3>
                    <a href="add_transaction.php" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Tambah Transaksi
                    </a>
                </div>
                <table class="table table-bordered table-striped table-hover display">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Jumlah</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <?php if ($isAdmin): ?>
                                <th>Username</th>
                            <?php endif; ?>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="<?= $isAdmin ? '7' : '6' ?>" class="text-center">Belum ada transaksi.</td>
                            </tr>
                        <?php else:
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="<?= $row['transaction_type'] == 'in' ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($row['amount'], 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $row['transaction_type'] == 'in' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $row['transaction_type'] == 'in' ? 'Pemasukan' : 'Pengeluaran'; ?>
                                        </span>
                                    </td>
                                    <td><?= $row['description']; ?></td>
                                    <td><?= $row['created_at']; ?></td>
                                    <?php if ($isAdmin): ?>
                                        <td><?= $row['username']; ?></td>
                                    <?php endif; ?>
                                    <td class="action-buttons">
                                        <a href="edit_transaction.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete_transaction.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Saldo -->
        <div class="saldo-section">
            <div class="top-card bg-success">
                <i class="fas fa-arrow-down"></i>
                <h4>Pemasukan</h4>
                <p>Rp <?= number_format($in, 0, ',', '.'); ?></p>
            </div>
            <div class="top-card bg-danger">
                <i class="fas fa-arrow-up"></i>
                <h4>Pengeluaran</h4>
                <p>Rp <?= number_format($out, 0, ',', '.'); ?></p>
            </div>
            <div class="top-card bg-primary">
                <i class="fas fa-wallet"></i>
                <h4>Saldo Bersih</h4>
                <p>Rp <?= number_format($saldo, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Aplikasi Keuangan. All Rights Reserved.</p>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>

</body>
</html>
