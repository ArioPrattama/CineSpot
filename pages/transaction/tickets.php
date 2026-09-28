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

$tab = isset($_GET['tab']) && $_GET['tab'] == 'past' ? 'past' : 'upcoming';

if($tab == 'upcoming'){
    $timeCondition = "Schedule.showTime >= NOW()";
} else {
    $timeCondition = "Schedule.showTime < NOW()";
}

$query = mysqli_query($conn,"
SELECT
Transaction.TransactionID,
Transaction.totalAmount,
Schedule.showTime,
Movie.title,
Movie.poster,
Studio.name AS studioName
FROM Transaction
JOIN Schedule ON Transaction.ScheduleID = Schedule.ScheduleID
JOIN Movie ON Schedule.MovieID = Movie.MovieID
JOIN Studio ON Schedule.StudioID = Studio.StudioID
WHERE Transaction.UserID='$UserID'
AND Transaction.paymentStatus='Paid'
AND $timeCondition
ORDER BY Schedule.showTime ".($tab == 'upcoming' ? "ASC" : "DESC")."
");
?>

<div class="container mt-3">

<h3 class="page-title">🎟 My Ticket</h3>

<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link <?php echo $tab=='upcoming'?'active':''; ?>" href="?tab=upcoming">Akan Datang</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $tab=='past'?'active':''; ?>" href="?tab=past">Selesai</a>
    </li>
</ul>

<?php if(mysqli_num_rows($query) == 0){ ?>

    <div class="text-center text-muted mt-5">
        <h1>🎬</h1>
        <p>Belum ada tiket <?php echo $tab=='upcoming' ? 'yang akan datang' : 'riwayat'; ?>.</p>
        <a href="../../index.php" class="btn btn-primary">Pesan Tiket Sekarang</a>
    </div>

<?php } ?>

<?php while($t = mysqli_fetch_assoc($query)){ ?>

    <a href="detail.php?id=<?php echo $t['TransactionID']; ?>" class="text-decoration-none text-dark">
        <div class="card ticket-card mb-3">
            <div class="ticket-header">
                <h6 class="mb-0"><?php echo htmlspecialchars($t['title']); ?></h6>
            </div>
            <div class="ticket-body">
                <p class="mb-1">🏢 <?php echo htmlspecialchars($t['studioName']); ?></p>
                <p class="mb-1">🕒 <?php echo date("d M Y, H:i", strtotime($t['showTime'])); ?></p>
                <p class="mb-0 d-flex justify-content-between">
                    <span>Total</span>
                    <b>Rp <?php echo number_format($t['totalAmount'],0,',','.'); ?></b>
                </p>
            </div>
        </div>
    </a>

<?php } ?>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
