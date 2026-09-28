<?php
session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";

$UserID = $_SESSION['UserID'];

$success = "";
$error = "";

if(isset($_POST['update_profile'])){

    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    if($fullname == ""){

        $error = "Nama lengkap tidak boleh kosong!";

    }else{

        mysqli_query($conn,"
        UPDATE User
        SET fullName='$fullname', phone='$phone'
        WHERE UserID='$UserID'
        ");

        $_SESSION['fullName'] = $fullname;
        $success = "Profil berhasil diperbarui!";

    }

}

$query = mysqli_query($conn,"SELECT * FROM User WHERE UserID='$UserID'");
$user = mysqli_fetch_assoc($query);

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-3">

<h3 class="page-title">👤 Profile</h3>

<?php if($success){ ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php if($error){ ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<div class="card mb-3">
    <div class="card-body">

        <p class="mb-1"><b><?php echo htmlspecialchars($user['fullName']); ?></b></p>
        <p class="mb-1 text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
        <p class="mb-0">⭐ Loyalty Points : <b><?php echo (int)$user['loyaltyPoints']; ?></b></p>

    </div>
</div>

<div class="card mb-4">
    <div class="card-body">

        <h6 class="mb-3">Edit Data Diri</h6>

        <form method="POST">

            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" class="form-control" name="fullname"
                       value="<?php echo htmlspecialchars($user['fullName']); ?>" required>
            </div>

            <div class="mb-3">
                <label>No HP</label>
                <input type="text" class="form-control" name="phone"
                       value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary w-100">
                Simpan Perubahan
            </button>

        </form>

    </div>
</div>

<div class="profile-menu">

    <a href="../transaction/tickets.php">
        <span>🎟 My Ticket</span> <span>›</span>
    </a>

    <a href="../transaction/history.php">
        <span>📜 Riwayat Transaksi</span> <span>›</span>
    </a>

    <a href="../review/index.php">
        <span>⭐ Review Film</span> <span>›</span>
    </a>

    <a href="../fnb/index.php">
        <span>🍿 Menu F&amp;B</span> <span>›</span>
    </a>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){ ?>
    <a href="../../admin/index.php">
        <span>🔧 Admin Panel</span> <span>›</span>
    </a>
    <?php } ?>

    <a href="../../logout.php" class="text-danger">
        <span>🚪 Logout</span> <span>›</span>
    </a>

</div>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>
