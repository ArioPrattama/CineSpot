<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";
include "../../includes/header.php";
include "../../includes/navbar.php";

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if($keyword != ''){

    $keywordEsc = mysqli_real_escape_string($conn, $keyword);

    $movies = mysqli_query($conn,"
    SELECT * FROM Movie
    WHERE title LIKE '%$keywordEsc%'
    OR genre LIKE '%$keywordEsc%'
    ORDER BY title
    ");

}else{

    $movies = mysqli_query($conn,"SELECT * FROM Movie ORDER BY title");

}
?>

<div class="container mt-3">

<form action="search.php" method="GET" class="mb-3">

<div class="input-group">

<span class="input-group-text">🔍</span>

<input
type="text"
name="q"
class="form-control"
placeholder="Cari film..."
value="<?php echo htmlspecialchars($keyword); ?>">

<button type="submit" class="btn btn-primary">Cari</button>

</div>

</form>

<h5 class="mb-3">

<?php echo $keyword != '' ? "Hasil pencarian \"".htmlspecialchars($keyword)."\"" : "Semua Film"; ?>

</h5>

<?php if(mysqli_num_rows($movies) == 0){ ?>

    <div class="text-center text-muted mt-5">
        <h1>🔎</h1>
        <p>Film tidak ditemukan.</p>
    </div>

<?php } ?>

<div class="row">

<?php while($m = mysqli_fetch_assoc($movies)){ ?>

<div class="col-6 mb-3">

<div class="card">

<?php
$posterSrc = $m['poster'];
if($posterSrc != '' && !preg_match('/^https?:\/\//', $posterSrc)){
    $posterSrc = "/cinespot/".$posterSrc;
}
?>
<img src="<?php echo htmlspecialchars($posterSrc); ?>" class="movie-poster">

<div class="card-body">

<h6><?php echo htmlspecialchars($m['title']); ?></h6>

⭐ <?php echo htmlspecialchars($m['rating']); ?>/10
<br>
⏱ <?php echo $m['duration']; ?> Menit
<br><br>

<a href="detail.php?id=<?php echo $m['MovieID']; ?>" class="btn btn-primary w-100">
    Pesan Tiket
</a>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
