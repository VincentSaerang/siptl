<?php
session_start();
include 'database.php';

// Cek hak akses
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'inputer_inspektorat') {
    header("Location: dashboard.php");
    exit();
}

// Ambil data temuan
$temuans = $conn->query("SELECT * FROM temuan")->fetchAll(PDO::FETCH_ASSOC);

// Proses tambah temuan
if (isset($_POST['lhp_id']) && empty($_POST['temuan_id'])) {
    $lhp_id = $_POST['lhp_id'];
    $description = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO temuan (lhp_id, description) VALUES (?, ?)");
    $stmt->execute([$lhp_id, $description]);

    header("Location: temuan.php");
    exit();
}

// Proses edit temuan
if (isset($_GET['edit'])) {
    $temuan_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM temuan WHERE temuan_id = ?");
    $stmt->execute([$temuan_id]);
    $temuan_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['lhp_id']) && !empty($_POST['temuan_id'])) {
        $lhp_id = $_POST['lhp_id'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE temuan SET lhp_id = ?, description = ? WHERE temuan_id = ?");
        $stmt->execute([$lhp_id, $description, $temuan_id]);

        header("Location: temuan.php");
        exit();
    }
}

// Proses hapus temuan
if (isset($_GET['delete'])) {
    $temuan_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM temuan WHERE temuan_id = ?");
    $stmt->execute([$temuan_id]);

    header("Location: temuan.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Temuan</title>
</head>
<body>
    <h2>Manajemen Temuan</h2>
    <p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>    
    <h3>Tambah Temuan Baru</h3>
    <form method="POST">
        <label>LHP ID:</label>
        <select name="lhp_id" required>
            <?php foreach ($conn->query("SELECT * FROM lhp")->fetchAll(PDO::FETCH_ASSOC) as $lhp): ?>
                <option value="<?php echo $lhp['lhp_id']; ?>"><?php echo $lhp['lhp_id']; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Deskripsi:</label>
        <textarea name="description" required></textarea>
        <button type="submit">Tambah Temuan</button>
    </form>

    <h3>Daftar Temuan</h3>
    <table>
        <tr>
            <th>Temuan ID</th>
            <th>LHP ID</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($temuans as $temuan): ?>
            <tr>
                <td><?php echo $temuan['temuan_id']; ?></td>
                <td><?php echo $temuan['lhp_id']; ?></td>
                <td><?php echo $temuan['description']; ?></td>
                <td>
                    <a href="?edit=<?php echo $temuan['temuan_id']; ?>">Edit</a>
                    <a href="?delete=<?php echo $temuan['temuan_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if (isset($temuan_to_edit)): ?>
        <h3>Edit Temuan</h3>
        <form method="POST">
            <input type="hidden" name="temuan_id" value="<?php echo $temuan_to_edit['temuan_id']; ?>">
            <label>LHP ID:</label>
            <select name="lhp_id">
                <?php foreach ($conn->query("SELECT * FROM lhp")->fetchAll(PDO::FETCH_ASSOC) as $lhp): ?>
                    <option value="<?php echo $lhp['lhp_id']; ?>" <?php echo $temuan_to_edit['lhp_id'] == $lhp['lhp_id'] ? 'selected' : ''; ?>><?php echo $lhp['lhp_id']; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Deskripsi:</label>
            <textarea name="description" required><?php echo $temuan_to_edit['description']; ?></textarea>
            <button type="submit">Perbarui</button>
            <a href="temuan.php" style="text-decoration:none;"><button type="button">Batal</button></a>
        </form>
    <?php endif; ?>
</body>
</html>


