<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

if(isset($_POST['save'])){

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $price = (float)$_POST['price'];
    $image = mysqli_real_escape_string($conn, trim($_POST['image']));
    $stock = (int)$_POST['stock'];
    $status = $_POST['status'];
    $FnBID = (int)$_POST['FnBID'];

    if($name == ""){

        $error = "Nama item wajib diisi!";

    }else{

        if($FnBID > 0){

            mysqli_query($conn,"
            UPDATE FnB_Item SET
            name='$name', category='$category', price='$price',
            image='$image', stock='$stock', status='$status'
            WHERE FnBID='$FnBID'
            ");

            $success = "Item F&B berhasil diperbarui!";

        }else{

            mysqli_query($conn,"
            INSERT INTO FnB_Item (name, category, price, image, stock, status)
            VALUES ('$name','$category','$price','$image','$stock','$status')
            ");

            $success = "Item F&B berhasil ditambahkan!";

        }

    }

}

if(isset($_GET['delete'])){

    $FnBID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM FnB_Item WHERE FnBID='$FnBID'");
    header("Location: fnb.php");
    exit;

}

$editData = null;
if(isset($_GET['edit'])){

    $FnBID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM FnB_Item WHERE FnBID='$FnBID'");
    $editData = mysqli_fetch_assoc($q);

}

$items = mysqli_query($conn,"SELECT * FROM FnB_Item ORDER BY FnBID DESC");

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🍿 Kelola F&amp;B</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formFnB">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Item"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formFnB">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Item F&B" : "Tambah Item F&B Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="FnBID" value="<?php echo $editData['FnBID'] ?? 0; ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Nama Item</label>
                        <input type="text" name="name" class="form-control"
                               value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kategori</label>
                        <input type="text" name="category" class="form-control"
                               value="<?php echo htmlspecialchars($editData['category'] ?? ''); ?>" placeholder="Makanan / Minuman / Snack">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Harga</label>
                        <input type="number" step="0.01" name="price" class="form-control"
                               value="<?php echo $editData['price'] ?? ''; ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Stok</label>
                        <input type="number" name="stock" class="form-control"
                               value="<?php echo $editData['stock'] ?? '0'; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Available" <?php echo (($editData['status'] ?? '')=='Available')?'selected':''; ?>>Available</option>
                            <option value="Unavailable" <?php echo (($editData['status'] ?? '')=='Unavailable')?'selected':''; ?>>Unavailable</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>URL Gambar</label>
                        <input type="text" name="image" class="form-control"
                               value="<?php echo htmlspecialchars($editData['image'] ?? ''); ?>"
                               placeholder="assets/img/fnb/nama.jpg atau URL gambar">
                    </div>

                </div>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="fnb.php" class="btn btn-outline-secondary">Batal</a>
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
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($f = mysqli_fetch_assoc($items)){ ?>
                <tr>
                    <td><?php echo $f['FnBID']; ?></td>
                    <td><?php echo htmlspecialchars($f['name']); ?></td>
                    <td><?php echo htmlspecialchars($f['category']); ?></td>
                    <td>Rp <?php echo number_format($f['price'],0,',','.'); ?></td>
                    <td><?php echo $f['stock']; ?></td>
                    <td><span class="badge bg-<?php echo $f['status']=='Available'?'success':'secondary'; ?>"><?php echo $f['status']; ?></span></td>
                    <td>
                        <a href="fnb.php?edit=<?php echo $f['FnBID']; ?>#formFnB" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="fnb.php?delete=<?php echo $f['FnBID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus item ini?');">Hapus</a>
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
