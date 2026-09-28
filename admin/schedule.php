<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

if(isset($_POST['save'])){

    $MovieID = (int)$_POST['MovieID'];
    $StudioID = (int)$_POST['StudioID'];
    $showTime = $_POST['showTime'];
    $ticketPrice = (float)$_POST['ticketPrice'];
    $ScheduleID = (int)$_POST['ScheduleID'];

    if($MovieID == 0 || $StudioID == 0 || $showTime == ""){

        $error = "Movie, Studio, dan Jam Tayang wajib diisi!";

    }else{

        if($ScheduleID > 0){

            mysqli_query($conn,"
            UPDATE Schedule SET
            MovieID='$MovieID', StudioID='$StudioID',
            showTime='$showTime', ticketPrice='$ticketPrice'
            WHERE ScheduleID='$ScheduleID'
            ");

            $success = "Jadwal berhasil diperbarui!";

        }else{

            // availableSeat mengikuti kapasitas studio yang dipilih
            $studioData = mysqli_fetch_assoc(mysqli_query($conn,"SELECT capacity FROM Studio WHERE StudioID='$StudioID'"));
            $availableSeat = $studioData['capacity'] ?? 0;

            mysqli_query($conn,"
            INSERT INTO Schedule (MovieID, StudioID, showTime, ticketPrice, availableSeat)
            VALUES ('$MovieID','$StudioID','$showTime','$ticketPrice','$availableSeat')
            ");

            $success = "Jadwal berhasil ditambahkan!";

        }

    }

}

if(isset($_GET['delete'])){

    $ScheduleID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM Schedule WHERE ScheduleID='$ScheduleID'");
    header("Location: schedule.php");
    exit;

}

$editData = null;
if(isset($_GET['edit'])){

    $ScheduleID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM Schedule WHERE ScheduleID='$ScheduleID'");
    $editData = mysqli_fetch_assoc($q);

}

$schedules = mysqli_query($conn,"
SELECT Schedule.*, Movie.title, Studio.name AS studioName
FROM Schedule
JOIN Movie ON Schedule.MovieID = Movie.MovieID
JOIN Studio ON Schedule.StudioID = Studio.StudioID
ORDER BY Schedule.showTime DESC
");

$movieList = mysqli_query($conn,"SELECT * FROM Movie ORDER BY title");
$studioList = mysqli_query($conn,"
SELECT Studio.*, Cinema.name AS cinemaName
FROM Studio JOIN Cinema ON Studio.CinemaID=Cinema.CinemaID
ORDER BY Cinema.name, Studio.name
");
$studioArr = [];
while($row = mysqli_fetch_assoc($studioList)){ $studioArr[] = $row; }

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🗓 Kelola Schedule</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formSchedule">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Jadwal"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formSchedule">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Jadwal" : "Tambah Jadwal Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="ScheduleID" value="<?php echo $editData['ScheduleID'] ?? 0; ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Movie</label>
                        <select name="MovieID" class="form-control" required>
                            <option value="">Pilih Movie</option>
                            <?php while($m = mysqli_fetch_assoc($movieList)){ ?>
                                <option value="<?php echo $m['MovieID']; ?>"
                                    <?php echo (($editData['MovieID'] ?? '')==$m['MovieID'])?'selected':''; ?>>
                                    <?php echo htmlspecialchars($m['title']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Studio</label>
                        <select name="StudioID" class="form-control" required>
                            <option value="">Pilih Studio</option>
                            <?php foreach($studioArr as $s){ ?>
                                <option value="<?php echo $s['StudioID']; ?>"
                                    <?php echo (($editData['StudioID'] ?? '')==$s['StudioID'])?'selected':''; ?>>
                                    <?php echo htmlspecialchars($s['cinemaName']." - ".$s['name']." (".$s['type'].")"); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jam Tayang</label>
                        <input type="datetime-local" name="showTime" class="form-control"
                               value="<?php echo isset($editData['showTime']) ? date('Y-m-d\TH:i', strtotime($editData['showTime'])) : ''; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Harga Tiket</label>
                        <input type="number" step="0.01" name="ticketPrice" class="form-control"
                               value="<?php echo $editData['ticketPrice'] ?? ''; ?>" required>
                    </div>

                </div>

                <?php if(!$editData){ ?>
                    <small class="text-muted d-block mb-2">Jumlah kursi tersedia akan otomatis mengikuti kapasitas studio yang dipilih.</small>
                <?php } ?>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="schedule.php" class="btn btn-outline-secondary">Batal</a>
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
                    <th>Movie</th>
                    <th>Studio</th>
                    <th>Jam Tayang</th>
                    <th>Harga</th>
                    <th>Sisa Kursi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = mysqli_fetch_assoc($schedules)){ ?>
                <tr>
                    <td><?php echo $s['ScheduleID']; ?></td>
                    <td><?php echo htmlspecialchars($s['title']); ?></td>
                    <td><?php echo htmlspecialchars($s['studioName']); ?></td>
                    <td><?php echo date("d M Y, H:i", strtotime($s['showTime'])); ?></td>
                    <td>Rp <?php echo number_format($s['ticketPrice'],0,',','.'); ?></td>
                    <td><?php echo $s['availableSeat']; ?></td>
                    <td>
                        <a href="schedule.php?edit=<?php echo $s['ScheduleID']; ?>#formSchedule" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="schedule.php?delete=<?php echo $s['ScheduleID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus jadwal ini?');">Hapus</a>
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
