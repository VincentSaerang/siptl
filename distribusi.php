    <?php
session_start();
include 'database.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'inputer_inspektorat') {
    header("Location: dashboard.php");
    exit();
}

function distributeRecommendation($conn, $rekomendasi_id, $satker_id, $user_id, $tugas) {
    try {
        $stmt = $conn->prepare("INSERT INTO distribusi_rekomendasi (rekomendasi_id, user_id, satker_id, tugas, created_at) VALUES (:rekomendasi_id, :user_id, :satker_id, :tugas, NOW())");
        $stmt->bindParam(':rekomendasi_id', $rekomendasi_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':satker_id', $satker_id);
        $stmt->bindParam(':tugas', $tugas);
        $stmt->execute();
        return "Rekomendasi berhasil didistribusikan.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            return "Error: User dengan ID $user_id tidak ditemukan.";
        } else {
            return "Error: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rekomendasi_id = $_POST['rekomendasi_id'];
    $satker_id = $_POST['satker_id'];
    $user_id = $_SESSION['user_id'];
    $tugas = $_POST['tugas'];
    $message = distributeRecommendation($conn, $rekomendasi_id, $satker_id, $user_id, $tugas);
    echo $message;
}

if (isset($_GET['delete']) && $_SESSION['role'] == 'admin') {
    $distribusi_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM distribusi_rekomendasi WHERE distribusi_id = ?");
    $stmt->execute([$distribusi_id]);
    header("Location: distribusi.php");
    exit();
}

$rekomendasi_list = $conn->query("SELECT * FROM rekomendasi")->fetchAll(PDO::FETCH_ASSOC);
$satker_list = $conn->query("SELECT * FROM satker")->fetchAll(PDO::FETCH_ASSOC);
$distributed_list = $conn->query("SELECT dr.distribusi_id, r.recommendation_text, s.name AS satker_name, dr.tugas, dr.created_at
                                FROM distribusi_rekomendasi dr
                                JOIN rekomendasi r ON dr.rekomendasi_id = r.rekomendasi_id
                                JOIN satker s ON dr.satker_id = s.satker_id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Distribusi Rekomendasi</title>
    <style>
        table {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>
<p><a href="dashboard.php">Kembali ke halaman dashboard</a></p>    
    <h2>Distribusi Rekomendasi</h2>
    <form method="POST">
        <label>Rekomendasi ID:</label>
        <select name="rekomendasi_id">
            <?php foreach ($rekomendasi_list as $rekomendasi): ?>
                <option value="<?php echo $rekomendasi['rekomendasi_id']; ?>">
                    <?php echo $rekomendasi['recommendation_text']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Satker Tujuan:</label>
        <select name="satker_id">
            <?php foreach ($satker_list as $satker): ?>
                <option value="<?php echo $satker['satker_id']; ?>">
                    <?php echo $satker['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Tugas:</label>
        <textarea name="tugas" required></textarea>
        
        <button type="submit">Distribusikan</button>
    </form>
    <hr>
    <h3>Rekomendasi yang sudah didistribusikan:</h3>
    <table>
        <tr>
            <th>Rekomendasi ID</th>
            <th>Rekomendasi</th>
            <th>Satker Tujuan</th>
            <th>Tugas</th>
            <th>Tanggal / Jam</th>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <th>Aksi</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($distributed_list as $distributed): ?>
            <tr>
                <td><?php echo $distributed['distribusi_id']; ?></td>
                <td><?php echo $distributed['recommendation_text']; ?></td>
                <td><?php echo $distributed['satker_name']; ?></td>
                <td><?php echo $distributed['tugas']; ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($distributed['created_at'])); ?></td>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <td><a href="?delete=<?php echo $distributed['distribusi_id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>


