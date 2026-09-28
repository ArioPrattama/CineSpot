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

$id = (int)$_GET['id'];

$query = mysqli_query($conn,"SELECT * FROM Movie WHERE MovieID='$id'");

if(mysqli_num_rows($query)==0){
    echo "<div class='container mt-4'><h4>Film tidak ditemukan.</h4></div>";
    include "../../includes/footer.php";
    exit;
}

$movie = mysqli_fetch_assoc($query);
?>

<div class="container mt-3">

<img src="<?php echo $movie['poster']; ?>" class="movie-poster">

<h3 class="mt-3">
<?php echo $movie['title']; ?>
</h3>

<p>

⭐ Rating : <?php echo $movie['rating']; ?>/10

<br>

⏱ Durasi : <?php echo $movie['duration']; ?> Menit

</p>

<h5>📖 Sinopsis</h5>

<p>

<?php echo $movie['synopsis']; ?>

</p>

<hr>

<h5>🎟 Jadwal Tayang</h5>

<?php

$schedule = mysqli_query($conn,"
SELECT
Schedule.*,
Studio.name AS studioName
FROM Schedule
JOIN Studio
ON Schedule.StudioID = Studio.StudioID
WHERE MovieID='$id'
ORDER BY showTime
");

while($s = mysqli_fetch_assoc($schedule)){

?>

<div class="card mb-3">

<div class="card-body">

<b>

🏢 Studio :

<?php echo $s['studioName']; ?>

</b>

<br><br>

🕒

<?php

echo date("d M Y H:i",strtotime($s['showTime']));

?>

<br>

💰 Rp

<?php

echo number_format($s['ticketPrice'],0,',','.');

?>

<br><br>

<a

href="../booking/select_seat.php?id=<?php echo $s['ScheduleID']; ?>"

class="btn btn-primary w-100">

Pilih Kursi

</a>

</div>

</div>

<?php

}

?>

</div>

<?php

include "../../includes/bottom_nav.php";
include "../../includes/footer.php";

?>