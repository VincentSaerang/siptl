<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inputer_inspektorat') {
    header("Location: dashboard.php");
    exit();
}

// Fungsi untuk menampilkan data rekomendasi
function getRekomendasi() {
    global $conn;
    $rekoms = $conn->query("SELECT * FROM rekomendasi")->fetchAll(PDO::FETCH_ASSOC);
    return $rekoms;
}

// Fungsi untuk menambahkan data rekomendasi
function addRekomendasi($data) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO rekomendasi (temuan_id, recommendation_text, status) VALUES (:temuan_id, :text, :status)");
    $stmt->bindParam(':temuan_id', $data['temuan_id']);
    $stmt->bindParam(':text', $data['rek_text']);
    $stmt->bindParam(':status', $data['status']);
    $stmt->execute();
}

// Fungsi untuk mengedit data rekomendasi
function editRekomendasi($data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE rekomendasi SET temuan_id = :temuan_id, recommendation_text = :text, status = :status WHERE rekomendasi_id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->bindParam(':temuan_id', $data['temuan_id']);
    $stmt->bindParam(':text', $data['rek_text']);
    $stmt->bindParam(':status', $data['status']);
    $stmt->execute();
}

// Fungsi untuk menghapus data rekomendasi
function deleteRekomendasi($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM rekomendasi WHERE rekomendasi_id = ?");
    $stmt->execute([$id]);
}

// Proses pembuatan rekomendasi baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    addRekomendasi($_POST);
    echo "Rekomendasi baru berhasil dibuat.";
}

// Proses mengedit rekomendasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    editRekomendasi($_POST);
    echo "Rekomendasi berhasil diubah.";
    header("Location: rekomendasi.php");
    exit();
}

// Hapus rekomendasi
if (isset($_GET['delete'])) {
    deleteRekomendasi($_GET['delete']);
    echo "Rekomendasi berhasil dihapus.";
    header("Location: rekomendasi.php");
    exit();
}

$rekoms = getRekomendasi();
$tems = $conn->query("SELECT * FROM temuan")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rekomendasi</title>
</head>
<body>
    <h2>Rekomendasi</h2>

    <h3>Tambah Rekomendasi</h3>
    <form method="POST">
        <label>Rekomendasi:</label>
        <input type="text" name="rek_text" required>
        <label>Status:</label>
        <input type="text" name="status" required>
        <label>Temuan ID:</label>
        <select name="temuan_id" id="temuan_id">
            <?php foreach ($tems as $tem): ?>
                <option value="<?php echo $tem['temuan_id']; ?>"><?php echo $tem['temuan_id']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add">Tambah Rekomendasi</button>
    </form>

    <h3>Daftar Rekomendasi</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Temuan ID</th>
            <th>Rekomendasi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($rekoms as $rekom): ?>
        <tr>
            <td><?php echo $rekom['rekomendasi_id']; ?></td>
            <td><?php echo $rekom['temuan_id']; ?></td>
            <td><?php echo $rekom['recommendation_text']; ?></td>
            <td><?php echo $rekom['status']; ?></td>
            <td>
                <a href="?edit=<?php echo $rekom['rekomendasi_id']; ?>">Edit</a>
                <a href="?delete=<?php echo $rekom['rekomendasi_id']; ?>">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php if (isset($_GET['edit'])): ?>
    <h3>Edit Rekomendasi</h3>
    <?php $rekom_edit = $conn->query("SELECT * FROM rekomendasi WHERE rekomendasi_id = " . $_GET['edit'])->fetch(PDO::FETCH_ASSOC); ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $rekom_edit['rekomendasi_id']; ?>">
        <label>Rekomendasi:</label>
        <input type="text" name="rek_text" value="<?php echo $rekom_edit['recommendation_text']; ?>" required>
        <label>Status:</label>
        <input type="text" name="status" value="<?php echo $rekom_edit['status']; ?>" required>
        <label>Temuan ID:</label>
        <select name="temuan_id" id="temuan_id">
            <?php foreach ($tems as $tem): ?>
                <option value="<?php echo $tem['temuan_id']; ?>" <?php if ($tem['temuan_id'] == $rekom_edit['temuan_id']) echo 'selected'; ?>><?php echo $tem['temuan_id']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="edit">Edit Rekomendasi</button>
        <button type="button" onclick="location.href='rekomendasi.php'">Batal</button>
    </form>
    <?php endif; ?>
    <p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>
</body>
</html>


