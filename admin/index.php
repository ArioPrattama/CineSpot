<?php
require "includes/auth.php";
include "../config/database.php";

$totalMovie = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM Movie"))['total'];
$totalUser = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM User WHERE role='user'"))['total'];
$totalTransaksi = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM Transaction WHERE paymentStatus='Paid'"))['total'];
$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(totalAmount) AS total FROM Transaction WHERE paymentStatus='Paid'"))['total'];

include "includes/header.php";
?>

<h3 class="mb-4">📊 Dashboard</h3>

<div class="row">

    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Total Film</div>
                <h3><?php echo $totalMovie; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Total User</div>
                <h3><?php echo $totalUser; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Transaksi Sukses</div>
                <h3><?php echo $totalTransaksi; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted">Total Pendapatan</div>
                <h5>Rp <?php echo number_format($totalPendapatan ?? 0,0,',','.'); ?></h5>
            </div>
        </div>
    </div>

</div>

<div class="card stat-card mt-3">
    <div class="card-body">
        <h5>Selamat Datang, <?php echo htmlspecialchars($_SESSION['fullName']); ?> 👋</h5>
        <p class="text-muted mb-0">Gunakan menu di samping untuk mengelola Movie, Schedule, Cinema, Studio, Promo, dan F&amp;B.</p>
    </div>
</div>

<?php
include "includes/footer.php";
?>
