<?php
session_start();
include('includes/db_connect.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query untuk memeriksa apakah username dan password valid
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Aplikasi Keuangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background Dark Elegan */
        body {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* Card Login Desain Elegan */
        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            background-color: #1c2833;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 2rem;
            color: #ecf0f1;
        }

        /* Title */
        .card-title {
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            color: #ecf0f1;
        }

        /* Form Labels */
        .form-label {
            font-weight: 600;
            color: #bdc3c7;
        }

        /* Input Fields */
        .form-control {
            border-radius: 15px;
            padding: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #34495e;
            color: #ecf0f1;
            border: 1px solid #2c3e50;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 150, 136, 0.6);
            border-color: #16a085;
        }

        .form-control::placeholder {
            color: #7f8c8d;
        }

        /* Button Style */
        .btn-primary {
            background-color: #16a085;
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1abc9c;
            transform: translateY(-3px);
        }

        /* Alert Error Style */
        .alert {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #ecf0f1;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .card {
                width: 90%;
                max-width: 350px;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-body">
        <h3 class="card-title">Login</h3>

        <!-- Tampilkan pesan jika ada error -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan Username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</div>

<!-- Footer Section Tanpa Syarat dan Ketentuan -->
<div class="footer">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
