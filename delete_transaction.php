<?php
session_start();
include('includes/db_connect.php');

$id = $_GET['id'];
$conn->query("DELETE FROM transactions WHERE id = $id");
header("Location: dashboard.php");
