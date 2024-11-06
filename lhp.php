<?php
include 'database.php';

if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $bpk_issue_date = $_POST['bpk_issue_date'] ?: date('Y-m-d');
    if (isset($_POST['lhp_id'])) {
        $lhp_id = $_POST['lhp_id'];
        $stmt = $conn->prepare("UPDATE lhp SET title = ?, bpk_issue_date = ? WHERE lhp_id = ?");
        $stmt->execute([$title, $bpk_issue_date, $lhp_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO lhp (title, bpk_issue_date) VALUES (?, ?)");
        $stmt->execute([$title, $bpk_issue_date]);
    }

    header("Location: lhp.php");
    exit();
}

if (isset($_GET['edit'])) {
    $lhp_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM lhp WHERE lhp_id = ?");
    $stmt->execute([$lhp_id]);
    $lhp_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $bpk_issue_date = $_POST['bpk_issue_date'] ?: date('Y-m-d');
        $stmt = $conn->prepare("UPDATE lhp SET title = ?, bpk_issue_date = ? WHERE lhp_id = ?");
        $stmt->execute([$title, $bpk_issue_date, $lhp_id]);

        header("Location: lhp.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $lhp_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM lhp WHERE lhp_id = ?");
    $stmt->execute([$lhp_id]);
}

$lhps = $conn->query("SELECT * FROM lhp")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen LHP</title>
</head>
<body>
    <h2>Manajemen LHP</h2>
    <form method="POST">
        <label>Judul:</label>
        <input type="text" name="title" required><br>

        <label>Tanggal Terbit:</label>
        <input type="date" name="bpk_issue_date"><br>

        <button type="submit">Tambah LHP</button>
    </form>

    <?php if (isset($lhp_to_edit)): ?>
        <h3>Edit LHP</h3>
        <form method="POST">
            <input type="hidden" name="lhp_id" value="<?php echo $lhp_to_edit['lhp_id']; ?>">
            <label>Judul:</label>
            <input type="text" name="title" value="<?php echo $lhp_to_edit['title']; ?>" required><br>

            <label>Tanggal Terbit:</label>
            <input type="date" name="bpk_issue_date" value="<?php echo $lhp_to_edit['bpk_issue_date']; ?>"><br>

            <button type="submit">Perbarui LHP</button>
            <a href="lhp.php" style="text-decoration:none;"><button type="button">Batal</button></a>
        </form>
    <?php endif; ?>

    <table border="1">
        <tr>
            <th>LHP ID</th>
            <th>Judul</th>
            <th>Tanggal Terbit</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($lhps as $lhp): ?>
        <tr>
            <td><?php echo $lhp['lhp_id']; ?></td>
            <td><?php echo $lhp['title']; ?></td>
            <td><?php echo $lhp['bpk_issue_date']; ?></td>
            <td>
                <a href="?edit=<?php echo $lhp['lhp_id']; ?>">Edit</a>
                <a href="?delete=<?php echo $lhp['lhp_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>
</body>
</html>

