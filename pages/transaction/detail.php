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
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');

if(!isset($_GET['id'])){
    header("Location: ../../index.php");
    exit;
}

$TransactionID = (int)$_GET['id'];

$query = mysqli_query($conn,"
SELECT
Transaction.*,
Schedule.showTime,
Schedule.ticketPrice,
Movie.title,
Movie.poster,
Movie.duration,
Studio.name AS studioName,
Studio.type AS studioType,
Cinema.name AS cinemaName,
Cinema.city AS cinemaCity,
Promo.promoCode,
Promo.discount AS promoDiscount
FROM Transaction
JOIN Schedule ON Transaction.ScheduleID = Schedule.ScheduleID
JOIN Movie ON Schedule.MovieID = Movie.MovieID
JOIN Studio ON Schedule.StudioID = Studio.StudioID
JOIN Cinema ON Studio.CinemaID = Cinema.CinemaID
LEFT JOIN Promo ON Transaction.PromoID = Promo.PromoID
WHERE Transaction.TransactionID='$TransactionID'
");

if(mysqli_num_rows($query) == 0){
    echo "<div class='container mt-4'><h5>Transaksi tidak ditemukan.</h5></div>";
    include "../../includes/bottom_nav.php";
    include "../../includes/footer.php";
    exit;
}

$trx = mysqli_fetch_assoc($query);

// Pastikan hanya pemilik transaksi (atau admin) yang bisa melihat
if($trx['UserID'] != $UserID && !$isAdmin){
    echo "<div class='container mt-4'><h5>Anda tidak memiliki akses ke transaksi ini.</h5></div>";
    include "../../includes/bottom_nav.php";
    include "../../includes/footer.php";
    exit;
}

// Ambil kursi
$seatQuery = mysqli_query($conn,"
SELECT Seat.seatRow, Seat.seatNumber
FROM Transaction_Seat
JOIN Seat ON Transaction_Seat.SeatID = Seat.SeatID
WHERE Transaction_Seat.TransactionID='$TransactionID'
ORDER BY Seat.seatRow, Seat.seatNumber
");

$seats = [];
while($s = mysqli_fetch_assoc($seatQuery)){
    $seats[] = $s['seatRow'].$s['seatNumber'];
}

// Ambil F&B
$fnbQuery = mysqli_query($conn,"
SELECT FnB_Item.name, Transaction_FnB_Item.quantity, Transaction_FnB_Item.subtotal
FROM Transaction_FnB_Item
JOIN FnB_Item ON Transaction_FnB_Item.FnBID = FnB_Item.FnBID
WHERE Transaction_FnB_Item.TransactionID='$TransactionID'
");

$statusColor = [
    'Paid' => 'success',
    'Pending' => 'warning',
    'Cancelled' => 'danger'
];
$badgeColor = $statusColor[$trx['paymentStatus']] ?? 'secondary';
?>

<div class="container mt-3">

<h3 class="page-title">🎟 Detail Tiket</h3>

<div class="card ticket-card mb-3">

    <div class="ticket-header">
        <h5 class="mb-1"><?php echo htmlspecialchars($trx['title']); ?></h5>
        <small><?php echo htmlspecialchars($trx['cinemaName'])." - ".htmlspecialchars($trx['studioName'])." (".htmlspecialchars($trx['studioType']).")"; ?></small>
    </div>

    <div class="ticket-body">

        <p class="mb-1">🕒 <?php echo date("d M Y, H:i", strtotime($trx['showTime'])); ?> WIB</p>
        <p class="mb-1">💺 Kursi : <b><?php echo count($seats) ? implode(", ", $seats) : "-"; ?></b></p>
        <p class="mb-1">🏙 <?php echo htmlspecialchars($trx['cinemaCity']); ?></p>

        <hr>

        <p class="mb-1">Kode Transaksi : <b>#<?php echo $trx['TransactionID']; ?></b></p>
        <p class="mb-1">QRIS Ref : <?php echo htmlspecialchars($trx['qris']); ?></p>
        <p class="mb-1">Tanggal Beli : <?php echo date("d M Y, H:i", strtotime($trx['createdDate'])); ?></p>
        <p class="mb-2">Status :
            <span class="badge bg-<?php echo $badgeColor; ?> badge-status">
                <?php echo $trx['paymentStatus']; ?>
            </span>
        </p>

        <?php if(mysqli_num_rows($fnbQuery) > 0){ ?>
        <hr>
        <p class="mb-1"><b>🍿 Makanan & Minuman</b></p>
        <?php while($f = mysqli_fetch_assoc($fnbQuery)){ ?>
            <p class="mb-1 d-flex justify-content-between">
                <span><?php echo htmlspecialchars($f['name'])." x".$f['quantity']; ?></span>
                <span>Rp <?php echo number_format($f['subtotal'],0,',','.'); ?></span>
            </p>
        <?php } ?>
        <?php } ?>

        <?php if($trx['promoCode']){ ?>
        <p class="mb-1 text-success">🎁 Promo <?php echo htmlspecialchars($trx['promoCode']); ?> (-<?php echo $trx['promoDiscount']; ?>%)</p>
        <?php } ?>

        <hr>

        <p class="mb-0 d-flex justify-content-between">
            <b>Total Bayar</b>
            <b>Rp <?php echo number_format($trx['totalAmount'],0,',','.'); ?></b>
        </p>

    </div>

</div>

<a href="tickets.php" class="btn btn-outline-primary w-100">Kembali ke My Ticket</a>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
