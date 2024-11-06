<?php
session_start();
include 'database.php';

// Cek apakah user adalah admin atau inputer_inspektorat
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inputer_inspektorat') {
    header("Location: dashboard.php");
    exit();
}

// Ambil data rekomendasi berdasarkan ID
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM rekomendasi WHERE id = ?");
$stmt->execute([$id]);
$rekomendasi = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lhp_id = $_POST['lhp_id'];
    $temuan_id = $_POST['temuan_id'];
    $status = $_POST['status'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("UPDATE rekomendasi SET lhp_id = ?, temuan_id = ?, status = ?, deskripsi = ? WHERE id = ?");
    $stmt->execute([$lhp_id, $temuan_id, $status, $deskripsi, $id]);
    
    header("Location: rekomendasi.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Rekomendasi</title>
</head>
<body>
    <h2>Edit Rekomendasi</h2>
    <form method="POST">
        <label>ID LHP:</label>
        <input type="number" name="lhp_id" value="<?php echo $rekomendasi['lhp_id']; ?>" required><br>

        <label>ID Temuan:</label>
        <input type="number" name="temuan_id" value="<?php echo $rekomendasi['temuan_id']; ?>" required><br>

        <label>Status:</label>
        <select name="status">
            <option value="belum_selesai" <?php if($rekomendasi['status'] == 'belum_selesai') echo 'selected'; ?>>Belum Selesai</option>
            <option value="selesai" <?php if($rekomendasi['status'] == 'selesai') echo 'selected'; ?>>Selesai</option>
        </select><br>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" required><?php echo $rekomendasi['deskripsi']; ?></textarea><br>

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>
