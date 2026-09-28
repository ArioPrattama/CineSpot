<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: login.php");
    exit;
}

include "config/database.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<div class="container mt-3">

<h4>
Halo,
<b><?php echo $_SESSION['fullName']; ?></b> 👋
</h4>

<p class="text-muted">
Mau nonton apa hari ini?
</p>

</div>

<div class="container mb-4">

<form action="pages/movie/search.php" method="GET">

<div class="input-group">

<span class="input-group-text">

🔍

</span>

<input
type="text"
name="q"
class="form-control"
placeholder="Cari film...">

</div>

</form>

</div>

<div class="container">

<div id="bannerPromo" class="carousel slide" data-bs-ride="carousel">

<div class="carousel-inner">

<div class="carousel-item active">

<img
src="assets/img/banner/banner1.jpg"
class="d-block w-100 rounded">

</div>

<div class="carousel-item">

<img
src="assets/img/banner/banner2.jpg"
class="d-block w-100 rounded">

</div>

<div class="carousel-item">

<img
src="assets/img/banner/banner3.jpg"
class="d-block w-100 rounded">

</div>

</div>

</div>

</div>

<div class="container mt-4">

<div class="d-flex justify-content-between">

<h5>🎬 Now Showing</h5>

<a href="pages/movie/search.php" style="text-decoration:none;">Lihat Semua</a>

</div>

<div class="row mt-3">

<?php

$movie = mysqli_query($conn,"SELECT * FROM Movie LIMIT 4");

while($m = mysqli_fetch_assoc($movie)){

?>

<div class="col-6 mb-3">

<div class="card">

<?php
$posterSrc = $m['poster'];
if($posterSrc != '' && !preg_match('/^https?:\/\//', $posterSrc)){
    $posterSrc = ltrim($posterSrc, '/');
}
?>
<img
src="<?php echo htmlspecialchars($posterSrc); ?>"
class="movie-poster">

<div class="card-body">

<h6>

<?php echo $m['title']; ?>

</h6>

⭐ <?php echo $m['rating']; ?>/10

<br>

⏱ <?php echo $m['duration']; ?> Menit

<br><br>

<a href="/cinespot/pages/movie/detail.php?id=<?php echo $m['MovieID']; ?>" class="btn btn-primary w-100">
    Pesan Tiket
</a>

</div>

</div>

</div>

<?php

}

?>

</div>

</div>

<?php
include "includes/bottom_nav.php";
include "includes/footer.php";
?>