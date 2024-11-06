<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    // Query statistik untuk pie chart
    $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM rekomendasi GROUP BY status");
    $stmt->execute();
    $rekomendasi_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total recommendations for percentage calculation
    $total_rekomendasi = array_sum(array_column($rekomendasi_stats, 'count'));
    
    // Query jumlah akun pengguna yang aktif
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $stmt->execute();
    $total_active_user = $stmt->fetchColumn();
    
    // Query jumlah total akun pengguna
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $total_user = $stmt->fetchColumn();
    
    // Query untuk memantau status rekomendasi yang telah didistribusikan
    $stmt = $conn->prepare("SELECT dr.distribusi_id, r.status, dr.rekomendasi_id, dr.user_id, dr.satker_id, dr.created_at
                            FROM distribusi_rekomendasi dr
                            JOIN rekomendasi r ON dr.rekomendasi_id = r.rekomendasi_id
                            GROUP BY dr.distribusi_id, r.status, dr.rekomendasi_id, dr.user_id, dr.satker_id, dr.created_at");
    $stmt->execute();
    $distribusi_rekomendasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

// Calculate percentage data
$labels = array_column($rekomendasi_stats, 'status');
$data = array_map(function($item) use ($total_rekomendasi) {
    return round(($item['count'] / $total_rekomendasi) * 100, 2);
}, $rekomendasi_stats);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        .navbar {
            background-color: #333;
            overflow: hidden;
            position: fixed;
            top: 0;
            width: 100%;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="distribusi.php">Distribusi</a>
        <a href="satker.php">Satker</a>
        <a href="temuan.php">Temuan</a>
        <a href="input_tl.php">Input TL</a>
        <a href="lhp.php">LHP</a>
        <a href="rekomendasi.php">Rekomendasi</a>
        <a href="user_management.php">User Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Dashboard</h2>
    
    <h3>Statistik Rekomendasi</h3>
    <canvas id="rekomendasiPieChart" style="width:400px;height:400px;"></canvas>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('rekomendasiPieChart').getContext('2d');
        var rekomendasiData = {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                data: <?php echo json_encode($data); ?>,
                backgroundColor: ['#4caf50', '#ff9800', '#ff5252']
            }]
        };
        new Chart(ctx, {
            type: 'pie',
            data: rekomendasiData,
            options: {
                responsive: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
    
    <h3>Jumlah Rekomendasi:
    <?php foreach ($rekomendasi_stats as $stat) {
        echo $stat['status'] . ': ' . $stat['count'] . ' (' . round(($stat['count'] / $total_rekomendasi) * 100, 2) . '%)<br>';
    } ?>
    </h3>
    
    <h3>Jumlah Akun Pengguna Aktif: <?php echo $total_active_user; ?></h3>
    <h3>Total Akun Pengguna: <?php echo $total_user; ?></h3>
    
    <h3>Daftar Rekomendasi yang Didistribusikan:</h3>
    <table>
        <tr>
            <th>Distribusi ID</th>
            <th>Rekomendasi ID</th>
            <th>Status</th>
            <th>User ID</th>
            <th>Satker ID</th>
            <th>Tanggal Distribusi</th>
        </tr>
        <?php foreach ($distribusi_rekomendasi as $rekomendasi) {
            echo '<tr>';
            echo '<td>' . $rekomendasi['distribusi_id'] . '</td>';
            echo '<td>' . $rekomendasi['rekomendasi_id'] . '</td>';
            echo '<td>' . $rekomendasi['status'] . '</td>';
            echo '<td>' . $rekomendasi['user_id'] . '</td>';
            echo '<td>' . $rekomendasi['satker_id'] . '</td>';
            echo '<td>' . $rekomendasi['created_at'] . '</td>';
            echo '</tr>';
        } ?>
    </table>
    
</body>
</html>


