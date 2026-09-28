<?php

session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../includes/header.php";
include "../../includes/navbar.php";

$TransactionID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

?>

<div class="container mt-5 text-center">

<h1>

✅

</h1>

<h3>

Pembayaran Berhasil

</h3>

<p>

Terima kasih telah memesan tiket di CineSpot.

</p>

<?php if($TransactionID > 0){ ?>

<a
href="../transaction/detail.php?id=<?php echo $TransactionID; ?>"
class="btn btn-primary w-100 mb-2">

🎟 Lihat Tiket Saya

</a>

<?php } ?>

<a
href="../../index.php"
class="btn btn-outline-success w-100">

Kembali ke Home

</a>

</div>

<?php

include "../../includes/bottom_nav.php";
include "../../includes/footer.php";

?>
