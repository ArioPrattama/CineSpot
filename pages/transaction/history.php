<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";
include "../../includes/header.php";
include "../../includes/navbar.php";

$UserID = $_SESSION['UserID'];

$query = mysqli_query($conn,"
SELECT
Transaction.TransactionID,
Transaction.totalAmount,
Transaction.paymentStatus,
Transaction.createdDate,
Movie.title,
Schedule.showTime
FROM Transaction
JOIN Schedule ON Transaction.ScheduleID = Schedule.ScheduleID
JOIN Movie ON Schedule.MovieID = Movie.MovieID
WHERE Transaction.UserID='$UserID'
ORDER BY Transaction.createdDate DESC
");

$statusColor = [
    'Paid' => 'success',
    'Pending' => 'warning',
    'Cancelled' => 'danger'
];
?>

<div class="container mt-3">

<h3 class="page-title">📜 Riwayat Transaksi</h3>

<?php if(mysqli_num_rows($query) == 0){ ?>

    <div class="text-center text-muted mt-5">
        <h1>🧾</h1>
        <p>Belum ada riwayat transaksi.</p>
    </div>

<?php } ?>

<?php while($t = mysqli_fetch_assoc($query)){
    $badgeColor = $statusColor[$t['paymentStatus']] ?? 'secondary';
?>

    <a href="detail.php?id=<?php echo $t['TransactionID']; ?>" class="text-decoration-none text-dark">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <b><?php echo htmlspecialchars($t['title']); ?></b><br>
                        <small class="text-muted">
                            #<?php echo $t['TransactionID']; ?> ·
                            <?php echo date("d M Y, H:i", strtotime($t['createdDate'])); ?>
                        </small>
                    </div>
                    <span class="badge bg-<?php echo $badgeColor; ?> badge-status">
                        <?php echo $t['paymentStatus']; ?>
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Jadwal: <?php echo date("d M Y, H:i", strtotime($t['showTime'])); ?></span>
                    <b>Rp <?php echo number_format($t['totalAmount'],0,',','.'); ?></b>
                </div>
            </div>
        </div>
    </a>

<?php } ?>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
