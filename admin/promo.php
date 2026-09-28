<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

if(isset($_POST['save'])){

    $promoCode = mysqli_real_escape_string($conn, trim($_POST['promoCode']));
    $discount = (float)$_POST['discount'];
    $validity = $_POST['validity'];
    $minimumPurchase = (float)$_POST['minimumPurchase'];
    $status = $_POST['status'];
    $PromoID = (int)$_POST['PromoID'];

    if($promoCode == ""){

        $error = "Kode promo wajib diisi!";

    }else{

        if($PromoID > 0){

            mysqli_query($conn,"
            UPDATE Promo SET
            promoCode='$promoCode', discount='$discount',
            validity='$validity', minimumPurchase='$minimumPurchase', status='$status'
            WHERE PromoID='$PromoID'
            ");

            $success = "Promo berhasil diperbarui!";

        }else{

            $check = mysqli_query($conn,"SELECT PromoID FROM Promo WHERE promoCode='$promoCode'");

            if(mysqli_num_rows($check) > 0){

                $error = "Kode promo sudah digunakan!";

            }else{

                mysqli_query($conn,"
                INSERT INTO Promo (promoCode, discount, validity, minimumPurchase, status)
                VALUES ('$promoCode','$discount','$validity','$minimumPurchase','$status')
                ");

                $success = "Promo berhasil ditambahkan!";

            }

        }

    }

}

if(isset($_GET['delete'])){

    $PromoID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM Promo WHERE PromoID='$PromoID'");
    header("Location: promo.php");
    exit;

}

$editData = null;
if(isset($_GET['edit'])){

    $PromoID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM Promo WHERE PromoID='$PromoID'");
    $editData = mysqli_fetch_assoc($q);

}

$promos = mysqli_query($conn,"SELECT * FROM Promo ORDER BY PromoID DESC");

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🎁 Kelola Promo</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formPromo">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Promo"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formPromo">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Promo" : "Tambah Promo Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="PromoID" value="<?php echo $editData['PromoID'] ?? 0; ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Kode Promo</label>
                        <input type="text" name="promoCode" class="form-control"
                               value="<?php echo htmlspecialchars($editData['promoCode'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Diskon (%)</label>
                        <input type="number" step="0.01" name="discount" class="form-control"
                               value="<?php echo $editData['discount'] ?? ''; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Berlaku Sampai</label>
                        <input type="date" name="validity" class="form-control"
                               value="<?php echo $editData['validity'] ?? ''; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Minimal Belanja</label>
                        <input type="number" step="0.01" name="minimumPurchase" class="form-control"
                               value="<?php echo $editData['minimumPurchase'] ?? '0'; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Active" <?php echo (($editData['status'] ?? '')=='Active')?'selected':''; ?>>Active</option>
                            <option value="Inactive" <?php echo (($editData['status'] ?? '')=='Inactive')?'selected':''; ?>>Inactive</option>
                        </select>
                    </div>

                </div>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="promo.php" class="btn btn-outline-secondary">Batal</a>
                <?php } ?>

            </form>

        </div>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body table-responsive">

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Min. Belanja</th>
                    <th>Berlaku Sampai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = mysqli_fetch_assoc($promos)){ ?>
                <tr>
                    <td><?php echo $p['PromoID']; ?></td>
                    <td><?php echo htmlspecialchars($p['promoCode']); ?></td>
                    <td><?php echo $p['discount']; ?>%</td>
                    <td>Rp <?php echo number_format($p['minimumPurchase'],0,',','.'); ?></td>
                    <td><?php echo $p['validity']; ?></td>
                    <td><span class="badge bg-<?php echo $p['status']=='Active'?'success':'secondary'; ?>"><?php echo $p['status']; ?></span></td>
                    <td>
                        <a href="promo.php?edit=<?php echo $p['PromoID']; ?>#formPromo" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="promo.php?delete=<?php echo $p['PromoID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus promo ini?');">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<?php
include "includes/footer.php";
?>
