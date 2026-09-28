<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";
include "../../includes/header.php";
include "../../includes/navbar.php";

if(!isset($_GET['id'])){
    header("Location: ../../index.php");
    exit;
}

$scheduleID = (int)$_GET['id'];

$schedule = mysqli_query($conn,"
SELECT
Schedule.*,
Movie.title
FROM Schedule
JOIN Movie
ON Schedule.MovieID = Movie.MovieID
WHERE ScheduleID='$scheduleID'
");

$data = mysqli_fetch_assoc($schedule);
?>

<div class="container mt-3">

<h4><?php echo $data['title']; ?></h4>

<p>

🕒 <?php echo date("d M Y H:i",strtotime($data['showTime'])); ?>

<br>

💰 Rp <?php echo number_format($data['ticketPrice'],0,',','.'); ?>

</p>

<hr>

<h5 class="text-center">

🎬 SCREEN

</h5>

<div class="screen mb-4"></div>

<form action="checkout.php" method="POST">

<input
type="hidden"
name="ScheduleID"
value="<?php echo $scheduleID; ?>">

<div class="seat-container">

<?php

$seat = mysqli_query($conn,"
SELECT *
FROM Seat
WHERE StudioID='{$data['StudioID']}'
ORDER BY seatRow,seatNumber
");

$currentRow="";

while($s=mysqli_fetch_assoc($seat)){

    if($currentRow!=$s['seatRow']){

        if($currentRow!=""){
            echo "</div>";
        }

        $currentRow=$s['seatRow'];

        echo "<div class='seat-row'>";
    }

    $isBooked = ($s['status'] == 'Booked');

?>

<label class="seat <?php echo $isBooked ? 'seat-booked' : ''; ?>">

<input
type="checkbox"
name="seat[]"
value="<?php echo $s['SeatID']; ?>"
<?php echo $isBooked ? 'disabled' : ''; ?>>

<span>

<?php

echo $s['seatRow'].$s['seatNumber'];

?>

</span>

</label>

<?php

}

echo "</div>";

?>

</div>

<button
class="btn btn-primary w-100 mt-4">

Lanjut Checkout

</button>

</form>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>