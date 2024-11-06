<?php
session_start();
include 'database.php';

// Cek apakah user adalah admin atau inputer_inspektorat
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inputer_inspektorat') {
    header("Location: dashboard.php");
    exit();
}

// Hapus rekomendasi berdasarkan ID
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM rekomendasi WHERE id = ?");
$stmt->execute([$id]);

header("Location: rekomendasi.php");
exit();
?>
