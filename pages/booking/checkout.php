<?php

session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";
include "../../includes/header.php";
include "../../includes/navbar.php";

if(
!isset($_POST['ScheduleID'])
||
!isset($_POST['seat'])
){

    header("Location: ../../index.php");
    exit;

}

$ScheduleID = (int)$_POST['ScheduleID'];

$seat = $_POST['seat'];

$query = mysqli_query($conn,"
SELECT
Schedule.*,
Movie.title
FROM Schedule
JOIN Movie
ON Schedule.MovieID=Movie.MovieID
WHERE ScheduleID='$ScheduleID'
");

$data=mysqli_fetch_assoc($query);

$totalSeat=count($seat);

$totalHarga=$totalSeat*$data['ticketPrice'];

?>

<div class="container mt-3">

<h3>

🧾 Checkout

</h3>

<hr>

<h5>

<?php echo $data['title']; ?>

</h5>

<p>

Jumlah Kursi :

<b>

<?php echo $totalSeat; ?>

</b>

</p>

<p>

Harga Tiket :

Rp

<?php

echo number_format($data['ticketPrice'],0,',','.');

?>

</p>

<p>

Total :

<h4>

Rp

<?php

echo number_format($totalHarga,0,',','.');

?>

</h4>

</p>

<hr>

<h5>

🍿 Tambah Makanan & Minuman

</h5>

<form
action="process_transaction.php"
method="POST">

<input
type="hidden"
name="ScheduleID"
value="<?php echo $ScheduleID; ?>">

<?php

foreach($seat as $s){

?>

<input
type="hidden"
name="seat[]"
value="<?php echo $s; ?>">

<?php

}

?>

<?php

$fnb=mysqli_query($conn,"
SELECT *
FROM FnB_Item
");

while($f=mysqli_fetch_assoc($fnb)){

?>

<div class="card mb-2">

<div class="card-body">

<b>

<?php echo $f['name']; ?>

</b>

<br>

Rp

<?php

echo number_format($f['price'],0,',','.');

?>

<br><br>

Jumlah

<input

type="number"

class="form-control"

name="fnb[<?php echo $f['FnBID']; ?>]"

value="0"

min="0">

</div>

</div>

<?php

}

?>

<hr>

<label>

🎁 Kode Promo

</label>

<input

type="text"

class="form-control"

name="promo">

<br>

<button

class="btn btn-success w-100">

Bayar Sekarang

</button>

</form>

</div>

<?php

include "../../includes/bottom_nav.php";

include "../../includes/footer.php";

?>