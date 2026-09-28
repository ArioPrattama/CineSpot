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
$success = "";
$error = "";

// Simpan / update review
if(isset($_POST['submit_review'])){

    $MovieID = (int)$_POST['MovieID'];
    $ratingScore = (int)$_POST['ratingScore'];
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));

    if($ratingScore < 1 || $ratingScore > 5){

        $error = "Rating harus antara 1-5!";

    }else{

        // cek apakah user memang pernah menonton film ini
        $checkWatch = mysqli_query($conn,"
        SELECT Transaction.TransactionID
        FROM Transaction
        JOIN Schedule ON Transaction.ScheduleID = Schedule.ScheduleID
        WHERE Transaction.UserID='$UserID'
        AND Schedule.MovieID='$MovieID'
        AND Transaction.paymentStatus='Paid'
        LIMIT 1
        ");

        if(mysqli_num_rows($checkWatch) == 0){

            $error = "Anda hanya bisa mereview film yang sudah dibeli tiketnya.";

        }else{

            $checkReview = mysqli_query($conn,"
            SELECT ReviewID FROM Review
            WHERE MovieID='$MovieID' AND UserID='$UserID'
            ");

            if(mysqli_num_rows($checkReview) > 0){

                $row = mysqli_fetch_assoc($checkReview);

                mysqli_query($conn,"
                UPDATE Review
                SET ratingScore='$ratingScore', comment='$comment'
                WHERE ReviewID='{$row['ReviewID']}'
                ");

            }else{

                mysqli_query($conn,"
                INSERT INTO Review (MovieID, UserID, ratingScore, comment)
                VALUES ('$MovieID','$UserID','$ratingScore','$comment')
                ");

            }

            $success = "Review berhasil disimpan!";

        }

    }

}

// Ambil daftar film yang sudah ditonton (paid) + review milik user (jika ada)
$movies = mysqli_query($conn,"
SELECT DISTINCT
Movie.MovieID, Movie.title, Movie.poster,
Review.ratingScore, Review.comment
FROM Transaction
JOIN Schedule ON Transaction.ScheduleID = Schedule.ScheduleID
JOIN Movie ON Schedule.MovieID = Movie.MovieID
LEFT JOIN Review ON Review.MovieID = Movie.MovieID AND Review.UserID = Transaction.UserID
WHERE Transaction.UserID='$UserID'
AND Transaction.paymentStatus='Paid'
ORDER BY Movie.title
");
?>

<div class="container mt-3">

<h3 class="page-title">⭐ Review Film</h3>

<?php if($success){ ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php if($error){ ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<?php if(mysqli_num_rows($movies) == 0){ ?>

    <div class="text-center text-muted mt-5">
        <h1>🎬</h1>
        <p>Anda belum menonton film apapun. Beli tiket dulu untuk bisa memberi review.</p>
    </div>

<?php } ?>

<?php while($m = mysqli_fetch_assoc($movies)){
    $collapseId = "review".$m['MovieID'];
?>

    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">
                <b><?php echo htmlspecialchars($m['title']); ?></b>
                <button class="btn btn-sm btn-outline-primary" type="button"
                        data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>">
                    <?php echo $m['ratingScore'] ? "Ubah Review" : "Beri Review"; ?>
                </button>
            </div>

            <?php if($m['ratingScore']){ ?>
                <p class="star-rating mt-2 mb-0">
                    <?php echo str_repeat("⭐", (int)$m['ratingScore']); ?>
                </p>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($m['comment']); ?></p>
            <?php } ?>

            <div class="collapse mt-3" id="<?php echo $collapseId; ?>">

                <form method="POST">

                    <input type="hidden" name="MovieID" value="<?php echo $m['MovieID']; ?>">

                    <div class="mb-2">
                        <label>Rating (1-5)</label>
                        <select name="ratingScore" class="form-control" required>
                            <option value="">Pilih rating</option>
                            <?php for($i=1;$i<=5;$i++){ ?>
                                <option value="<?php echo $i; ?>" <?php echo ($m['ratingScore']==$i)?'selected':''; ?>>
                                    <?php echo str_repeat("⭐",$i); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Komentar</label>
                        <textarea name="comment" class="form-control" rows="3"><?php echo htmlspecialchars($m['comment'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="btn btn-primary w-100">
                        Simpan Review
                    </button>

                </form>

            </div>

        </div>
    </div>

<?php } ?>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
