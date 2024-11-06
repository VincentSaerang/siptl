<?php
session_start();
include 'database.php';

// Cek apakah user adalah admin atau inputer_inspektorat
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inputer_inspektorat')) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $lhp_id = $_POST['lhp_id'];
        $temuan_id = $_POST['temuan_id'];
        $status = $_POST['status'];
        $deskripsi = $_POST['deskripsi'];

        // Menyimpan data rekomendasi
        $stmt = $conn->prepare("INSERT INTO rekomendasi (lhp_id, temuan_id, status, deskripsi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lhp_id, $temuan_id, $status, $deskripsi]);

        header("Location: rekomendasi.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Rekomendasi</title>
</head>
<body>
    <h2>Tambah Rekomendasi</h2>
    <form method="POST">
        <label>ID LHP:</label>
        <input type="number" name="lhp_id" required><br>

        <label>ID Temuan:</label>
        <input type="number" name="temuan_id" required><br>

        <label>Status:</label>
        <select name="status">
            <option value="belum_selesai">Belum Selesai</option>
            <option value="selesai">Selesai</option>
        </select><br>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" required></textarea><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
