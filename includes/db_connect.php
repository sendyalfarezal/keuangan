<?php
$servername = "localhost";
$username = "root";
$password = ""; // Kosong default XAMPP
$dbname = "keuangan"; // HARUS sesuai nama database kamu

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
