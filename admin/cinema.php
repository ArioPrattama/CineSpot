<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

if(isset($_POST['save'])){

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $CinemaID = (int)$_POST['CinemaID'];

    if($name == ""){

        $error = "Nama cinema wajib diisi!";

    }else{

        if($CinemaID > 0){

            mysqli_query($conn,"
            UPDATE Cinema SET name='$name', city='$city', address='$address'
            WHERE CinemaID='$CinemaID'
            ");

            $success = "Cinema berhasil diperbarui!";

        }else{

            mysqli_query($conn,"
            INSERT INTO Cinema (name, city, address)
            VALUES ('$name','$city','$address')
            ");

            $success = "Cinema berhasil ditambahkan!";

        }

    }

}

if(isset($_GET['delete'])){

    $CinemaID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM Cinema WHERE CinemaID='$CinemaID'");
    header("Location: cinema.php");
    exit;

}

$editData = null;
if(isset($_GET['edit'])){

    $CinemaID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM Cinema WHERE CinemaID='$CinemaID'");
    $editData = mysqli_fetch_assoc($q);

}

$cinemas = mysqli_query($conn,"SELECT * FROM Cinema ORDER BY CinemaID DESC");

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🏢 Kelola Cinema</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formCinema">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Cinema"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formCinema">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Cinema" : "Tambah Cinema Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="CinemaID" value="<?php echo $editData['CinemaID'] ?? 0; ?>">

                <div class="mb-3">
                    <label>Nama Cinema</label>
                    <input type="text" name="name" class="form-control"
                           value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label>Kota</label>
                    <input type="text" name="city" class="form-control"
                           value="<?php echo htmlspecialchars($editData['city'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($editData['address'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="cinema.php" class="btn btn-outline-secondary">Batal</a>
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
                    <th>Kota</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = mysqli_fetch_assoc($cinemas)){ ?>
                <tr>
                    <td><?php echo $c['CinemaID']; ?></td>
                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                    <td><?php echo htmlspecialchars($c['city']); ?></td>
                    <td><?php echo htmlspecialchars($c['address']); ?></td>
                    <td>
                        <a href="cinema.php?edit=<?php echo $c['CinemaID']; ?>#formCinema" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="cinema.php?delete=<?php echo $c['CinemaID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus cinema ini? Semua studio terkait juga akan terhapus.');">Hapus</a>
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
