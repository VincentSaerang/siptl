<?php
session_start();
include 'database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Proses penambahan atau pengeditan Satker
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $satker_id = isset($_POST['satker_id']) ? $_POST['satker_id'] : null;
    $name = $_POST['name'];
    $description = $_POST['description'] ?? '';
    if ($satker_id) {
        // Update existing Satker
        $stmt = $conn->prepare("UPDATE satker SET name = :name, description = :description WHERE satker_id = :satker_id");
        $stmt->bindParam(':satker_id', $satker_id);
    } else {
        // Add new Satker
        $stmt = $conn->prepare("INSERT INTO satker (name, description) VALUES (:name, :description)");
    }
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    echo "Satker berhasil " . ($satker_id ? "diperbarui" : "ditambahkan") . ".";
    header("Location: satker.php");
    exit();
}

// Hapus Satker
if (isset($_GET['delete'])) {
    $satker_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM satker WHERE satker_id = ?");
    $stmt->execute([$satker_id]);
    echo "Satker berhasil dihapus.";
    header("Location: satker.php");
    exit();
}

// Ambil Satker untuk diedit
$satker_to_edit = null;
if (isset($_GET['edit'])) {
    $satker_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM satker WHERE satker_id = ?");
    $stmt->execute([$satker_id]);
    $satker_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil daftar Satker
$satkers = $conn->query("SELECT * FROM satker")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Satker</title>
</head>
<body>
    <h2>Manajemen Satker</h2>
    <form method="POST">
        <input type="hidden" name="satker_id" value="<?php echo isset($satker_to_edit['satker_id']) ? $satker_to_edit['satker_id'] : ''; ?>">
        <label>Nama Satker:</label>
        <input type="text" name="name" value="<?php echo isset($satker_to_edit['name']) ? $satker_to_edit['name'] : ''; ?>" required>
        <label>Deskripsi Satker:</label>
        <textarea name="description" required><?php echo isset($satker_to_edit['description']) ? $satker_to_edit['description'] : ''; ?></textarea>
        <button type="submit"><?php echo isset($satker_to_edit['satker_id']) ? 'Perbarui' : 'Tambah'; ?> Satker</button>
        <?php if (isset($satker_to_edit['satker_id'])): ?>
            <a href="satker.php" style="text-decoration:none;"><button type="button">Batal</button></a>
        <?php endif; ?>
    </form>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($satkers as $satker): ?>
        <tr>
            <td><?php echo $satker['satker_id']; ?></td>
            <td><?php echo $satker['name']; ?></td>
            <td><?php echo $satker['description']; ?></td>
            <td>
                <a href="?edit=<?php echo $satker['satker_id']; ?>">Edit</a>
                <a href="?delete=<?php echo $satker['satker_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>
</body>
</html>

