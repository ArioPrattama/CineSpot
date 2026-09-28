<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";
include "../../includes/header.php";
include "../../includes/navbar.php";

$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM FnB_Item WHERE status='Available'";
if($category != ''){
    $categoryEsc = mysqli_real_escape_string($conn, $category);
    $sql .= " AND category='$categoryEsc'";
}
$sql .= " ORDER BY category, name";

$fnb = mysqli_query($conn, $sql);

$categories = mysqli_query($conn, "SELECT DISTINCT category FROM FnB_Item WHERE status='Available' ORDER BY category");
?>

<div class="container mt-3">

<h3 class="page-title">🍿 Menu Food &amp; Beverage</h3>

<div class="mb-3">
    <a href="index.php" class="btn btn-sm <?php echo $category==''?'btn-primary':'btn-outline-primary'; ?>">Semua</a>
    <?php while($c = mysqli_fetch_assoc($categories)){ ?>
        <a href="index.php?category=<?php echo urlencode($c['category']); ?>"
           class="btn btn-sm <?php echo $category==$c['category']?'btn-primary':'btn-outline-primary'; ?>">
            <?php echo htmlspecialchars($c['category']); ?>
        </a>
    <?php } ?>
</div>

<p class="text-muted">Pesan F&amp;B dilakukan saat proses checkout tiket ya 🎬</p>

<div class="row">

<?php while($f = mysqli_fetch_assoc($fnb)){ ?>

    <div class="col-6 mb-3">
        <div class="card h-100">
            <?php if(!empty($f['image'])){ ?>
                <img src="<?php echo htmlspecialchars($f['image']); ?>" class="movie-poster" style="height:120px;">
            <?php } ?>
            <div class="card-body">
                <h6 class="mb-1"><?php echo htmlspecialchars($f['name']); ?></h6>
                <small class="text-muted"><?php echo htmlspecialchars($f['category']); ?></small>
                <p class="mb-0 mt-2">Rp <?php echo number_format($f['price'],0,',','.'); ?></p>
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
