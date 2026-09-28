<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

if(isset($_POST['save'])){

    $CinemaID = (int)$_POST['CinemaID'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $type = $_POST['type'];
    $capacity = (int)$_POST['capacity'];
    $StudioID = (int)$_POST['StudioID'];

    if($name == "" || $CinemaID == 0){

        $error = "Nama studio dan Cinema wajib diisi!";

    }else{

        if($StudioID > 0){

            mysqli_query($conn,"
            UPDATE Studio SET CinemaID='$CinemaID', name='$name', type='$type', capacity='$capacity'
            WHERE StudioID='$StudioID'
            ");

            $success = "Studio berhasil diperbarui!";

        }else{

            mysqli_query($conn,"
            INSERT INTO Studio (CinemaID, name, type, capacity)
            VALUES ('$CinemaID','$name','$type','$capacity')
            ");

            $newStudioID = mysqli_insert_id($conn);

            // Otomatis buat 'capacity' kursi kosong (5 kursi per baris A,B,C,...)
            $seatPerRow = 5;
            $rowLetters = range('A','Z');
            $rowIndex = 0;
            $seatInRow = 1;

            for($i=1; $i<=$capacity; $i++){

                if($seatInRow > $seatPerRow){
                    $rowIndex++;
                    $seatInRow = 1;
                }

                $row = $rowLetters[$rowIndex];

                mysqli_query($conn,"
                INSERT INTO Seat (StudioID, seatRow, seatNumber, status)
                VALUES ('$newStudioID','$row','$seatInRow','Available')
                ");

                $seatInRow++;

            }

            $success = "Studio berhasil ditambahkan beserta kursinya!";

        }

    }

}

if(isset($_GET['delete'])){

    $StudioID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM Studio WHERE StudioID='$StudioID'");
    header("Location: studio.php");
    exit;

}

$editData = null;
if(isset($_GET['edit'])){

    $StudioID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM Studio WHERE StudioID='$StudioID'");
    $editData = mysqli_fetch_assoc($q);

}

$studios = mysqli_query($conn,"
SELECT Studio.*, Cinema.name AS cinemaName
FROM Studio
JOIN Cinema ON Studio.CinemaID = Cinema.CinemaID
ORDER BY Studio.StudioID DESC
");

$cinemaList = mysqli_query($conn,"SELECT * FROM Cinema ORDER BY name");

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🎦 Kelola Studio</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formStudio">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Studio"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<?php if(!$editData){ ?>
<div class="alert alert-info">
    ℹ️ Saat menambah studio baru, kursi (Seat) akan otomatis dibuat sejumlah kapasitas (5 kursi per baris A, B, C, dst).
</div>
<?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formStudio">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Studio" : "Tambah Studio Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="StudioID" value="<?php echo $editData['StudioID'] ?? 0; ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Cinema</label>
                        <select name="CinemaID" class="form-control" required>
                            <option value="">Pilih Cinema</option>
                            <?php while($c = mysqli_fetch_assoc($cinemaList)){ ?>
                                <option value="<?php echo $c['CinemaID']; ?>"
                                    <?php echo (($editData['CinemaID'] ?? '')==$c['CinemaID'])?'selected':''; ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nama Studio</label>
                        <input type="text" name="name" class="form-control"
                               value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tipe</label>
                        <select name="type" class="form-control">
                            <option value="Regular" <?php echo (($editData['type'] ?? '')=='Regular')?'selected':''; ?>>Regular</option>
                            <option value="IMAX" <?php echo (($editData['type'] ?? '')=='IMAX')?'selected':''; ?>>IMAX</option>
                            <option value="Dolby" <?php echo (($editData['type'] ?? '')=='Dolby')?'selected':''; ?>>Dolby</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kapasitas Kursi</label>
                        <input type="number" name="capacity" class="form-control"
                               value="<?php echo $editData['capacity'] ?? ''; ?>" required
                               <?php echo $editData ? 'readonly' : ''; ?>>
                        <?php if($editData){ ?>
                            <small class="text-muted">Kapasitas tidak bisa diubah di sini agar jumlah kursi tetap konsisten. Kelola kursi secara manual jika perlu.</small>
                        <?php } ?>
                    </div>

                </div>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="studio.php" class="btn btn-outline-secondary">Batal</a>
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
                    <th>Cinema</th>
                    <th>Nama Studio</th>
                    <th>Tipe</th>
                    <th>Kapasitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = mysqli_fetch_assoc($studios)){ ?>
                <tr>
                    <td><?php echo $s['StudioID']; ?></td>
                    <td><?php echo htmlspecialchars($s['cinemaName']); ?></td>
                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                    <td><?php echo $s['type']; ?></td>
                    <td><?php echo $s['capacity']; ?></td>
                    <td>
                        <a href="studio.php?edit=<?php echo $s['StudioID']; ?>#formStudio" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="studio.php?delete=<?php echo $s['StudioID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus studio ini? Semua jadwal & kursi terkait juga akan terhapus.');">Hapus</a>
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
