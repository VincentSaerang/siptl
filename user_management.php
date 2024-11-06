<?php
session_start();
include 'database.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Fungsi untuk menambahkan user
function addUser($email, $password, $role, $satker_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO users (email, password, role, satker_id) VALUES (:email, :password, :role, :satker_id)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':satker_id', $satker_id);
    $stmt->execute();
}

// Fungsi untuk mengedit user
function editUser($user_id, $email, $role, $satker_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET email = :email, role = :role, satker_id = :satker_id WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':satker_id', $satker_id);
    $stmt->execute();
}

// Fungsi untuk mengaktifkan/menonaktifkan user
function toggleUserActive($user_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

// Fungsi untuk menghapus user
function deleteUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

// Proses pembuatan user baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $satker_id = $_POST['satker_id'] ?? null;

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "Email sudah terdaftar, silakan gunakan email lain";
    } else {
        addUser($email, $password, $role, $satker_id);
        echo "Pengguna baru berhasil dibuat.";
    }
}

// Proses pengeditan user
if (isset($_POST['edit'])) {
    $user_id = $_POST['user_id'];
    $user = $conn->query("SELECT * FROM users WHERE user_id = $user_id")->fetch(PDO::FETCH_ASSOC);
    $email = $_POST['email'] ?? $user['email'];
    $role = $_POST['role'] ?? $user['role'];
    $satker_id = $_POST['satker_id'] ?? $user['satker_id'] ?? null;

    if ($email != $user['email']) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND user_id != :user_id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "Email sudah terdaftar, silakan gunakan email lain";
        } else {
            editUser($user_id, $email, $role, $satker_id);
            echo "Pengguna berhasil diedit.";
        }
    } else {
        editUser($user_id, $email, $role, $satker_id);
        echo "Pengguna berhasil diedit.";
    }
}

// Mengaktifkan/Menonaktifkan user
if (isset($_GET['toggle_active'])) {
    $user_id = $_GET['toggle_active'];
    toggleUserActive($user_id);
    echo "Status pengguna berhasil diubah.";
}

// Menghapus user
if (isset($_POST['delete'])) {
    $user_id = $_POST['delete'];
    deleteUser($user_id);
    echo "Pengguna berhasil dihapus.";
}

// Ambil daftar pengguna
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
// Ambil daftar satker
$satker_list = $conn->query("SELECT * FROM satker")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Pengguna</title>
</head>
<body>
    <h2>Manajemen Pengguna</h2>
    <p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>    
    <h3>Tambah Pengguna Baru</h3>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Role:</label>
        <select name="role">
            <option value="inputer_inspektorat">Inputer Inspektorat</option>
            <option value="inputer_satker">Inputer Satker</option>
        </select>
        <label>Satker ID:</label>
        <select name="satker_id">
            <option value="">-- Pilih Satker --</option>
            <?php foreach ($satker_list as $satker): ?>
            <option value="<?php echo $satker['satker_id']; ?>"><?php echo $satker['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add">Tambah Pengguna</button>
    </form>

    <h3>Daftar Pengguna</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Satker ID</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['role']; ?></td>
            <td><?php echo $user['satker_id']; ?></td>
            <td><?php echo $user['is_active'] ? 'Aktif' : 'Nonaktif'; ?></td>
            <td>
                <a href="?toggle_active=<?php echo $user['user_id']; ?>">
                    <?php echo $user['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                </a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</button>
                </form>
                <button type="button" onclick="document.getElementById('edit_form').style.display='block';document.getElementById('edit_user_id').value='<?php echo $user['user_id']; ?>';document.getElementById('edit_email').value='<?php echo $user['email']; ?>';document.getElementById('edit_role').value='<?php echo $user['role']; ?>';document.getElementById('edit_satker_id').value='<?php echo $user['satker_id']; ?>';">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <form id="edit_form" method="POST" style="display:none;">
        <input type="hidden" id="edit_user_id" name="user_id">
        <label>Email:</label>
        <input type="email" id="edit_email" name="email" value="" required>
        <label>Role:</label>
        <select id="edit_role" name="role">
            <option value="inputer_inspektorat">Inputer Inspektorat</option>
            <option value="inputer_satker">Inputer Satker</option>
        </select>
        <label>Satker ID:</label>
        <select id="edit_satker_id" name="satker_id">
            <option value="">-- Pilih Satker --</option>
            <?php foreach ($satker_list as $satker): ?>
            <option value="<?php echo $satker['satker_id']; ?>"><?php echo $satker['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="edit">Edit</button>
        <button type="button" onclick="document.getElementById('edit_form').style.display='none'">Batal</button>
    </form>



