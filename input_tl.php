<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'inputer_inspektorat' && $_SESSION['role'] !== 'inputer_satker') {
    header("Location: dashboard.php");
    exit();
}

// Mengambil daftar rekomendasi untuk pilihan TL
$rekomendasi_list = $conn->query("SELECT * FROM rekomendasi")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rekomendasi_id = $_POST['rekomendasi_id'];
    $uraian = $_POST['uraian'];
    $nilai = $_POST['nilai'];

    $stmt = $conn->prepare("INSERT INTO usulan_tl (rekomendasi_id, user_id, uraian, nilai) VALUES (?, ?, ?, ?)");
    $stmt->execute([$rekomendasi_id, $_SESSION['user_id'], $uraian, $nilai]);
    $success = "Usulan Tindak Lanjut berhasil ditambahkan!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Usulan Tindak Lanjut</title>
</head>
<body>
    <h2>Input Usulan Tindak Lanjut</h2>
    <?php if (isset($success)) echo "<p>$success</p>"; ?>
    <form method="POST">
        <label>Rekomendasi:</label>
        <select name="rekomendasi_id">
            <?php foreach ($rekomendasi_list as $rekomendasi): ?>
                <option value="<?php echo $rekomendasi['id']; ?>"><?php echo $rekomendasi['deskripsi']; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Uraian:</label>
        <textarea name="uraian" required></textarea>
        <label>Nilai:</label>
        <input type="text" name="nilai" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
