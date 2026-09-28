<?php
require "includes/auth.php";
include "../config/database.php";

$error = "";
$success = "";

// CREATE / UPDATE
if(isset($_POST['save'])){

    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $synopsis = mysqli_real_escape_string($conn, trim($_POST['synopsis']));
    $duration = (int)$_POST['duration'];
    $poster = mysqli_real_escape_string($conn, trim($_POST['poster']));
    $rating = mysqli_real_escape_string($conn, trim($_POST['rating']));
    $genre = mysqli_real_escape_string($conn, trim($_POST['genre']));
    $releaseDate = $_POST['releaseDate'];
    $status = $_POST['status'];
    $MovieID = (int)$_POST['MovieID'];

    if($title == ""){

        $error = "Judul film wajib diisi!";

    }else{

        if($MovieID > 0){

            mysqli_query($conn,"
            UPDATE Movie SET
            title='$title', synopsis='$synopsis', duration='$duration',
            poster='$poster', rating='$rating', genre='$genre',
            releaseDate='$releaseDate', status='$status'
            WHERE MovieID='$MovieID'
            ");

            $success = "Film berhasil diperbarui!";

        }else{

            mysqli_query($conn,"
            INSERT INTO Movie
            (title, synopsis, duration, poster, rating, genre, releaseDate, status)
            VALUES
            ('$title','$synopsis','$duration','$poster','$rating','$genre','$releaseDate','$status')
            ");

            $success = "Film berhasil ditambahkan!";

        }

    }

}

// DELETE
if(isset($_GET['delete'])){

    $MovieID = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM Movie WHERE MovieID='$MovieID'");
    header("Location: movie.php");
    exit;

}

// Data untuk form edit (jika ada)
$editData = null;
if(isset($_GET['edit'])){

    $MovieID = (int)$_GET['edit'];
    $q = mysqli_query($conn,"SELECT * FROM Movie WHERE MovieID='$MovieID'");
    $editData = mysqli_fetch_assoc($q);

}

$movies = mysqli_query($conn,"SELECT * FROM Movie ORDER BY MovieID DESC");

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🎬 Kelola Movie</h3>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formMovie">
        <?php echo $editData ? "Tutup Form" : "+ Tambah Film"; ?>
    </button>
</div>

<?php if($success){ ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if($error){ ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<div class="collapse <?php echo $editData ? 'show' : ''; ?> mb-4" id="formMovie">
    <div class="card stat-card">
        <div class="card-body">

            <h5 class="mb-3"><?php echo $editData ? "Edit Film" : "Tambah Film Baru"; ?></h5>

            <form method="POST">

                <input type="hidden" name="MovieID" value="<?php echo $editData['MovieID'] ?? 0; ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Judul Film</label>
                        <input type="text" name="title" class="form-control"
                               value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Durasi (menit)</label>
                        <input type="number" name="duration" class="form-control"
                               value="<?php echo $editData['duration'] ?? ''; ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Rating (contoh: 8.5)</label>
                        <input type="text" name="rating" class="form-control"
                               value="<?php echo htmlspecialchars($editData['rating'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Genre</label>
                        <input type="text" name="genre" class="form-control"
                               value="<?php echo htmlspecialchars($editData['genre'] ?? ''); ?>" placeholder="Action, Drama">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Tanggal Rilis</label>
                        <input type="date" name="releaseDate" class="form-control"
                               value="<?php echo $editData['releaseDate'] ?? ''; ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Now Showing" <?php echo (($editData['status'] ?? '')=='Now Showing')?'selected':''; ?>>Now Showing</option>
                            <option value="Coming Soon" <?php echo (($editData['status'] ?? '')=='Coming Soon')?'selected':''; ?>>Coming Soon</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>URL Poster</label>
                        <input type="text" name="poster" class="form-control"
                               value="<?php echo htmlspecialchars($editData['poster'] ?? ''); ?>"
                               placeholder="assets/img/poster/judul.jpg atau URL gambar">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Sinopsis</label>
                        <textarea name="synopsis" class="form-control" rows="3"><?php echo htmlspecialchars($editData['synopsis'] ?? ''); ?></textarea>
                    </div>

                </div>

                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if($editData){ ?>
                    <a href="movie.php" class="btn btn-outline-secondary">Batal</a>
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
                    <th>Judul</th>
                    <th>Genre</th>
                    <th>Durasi</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($m = mysqli_fetch_assoc($movies)){ ?>
                <tr>
                    <td><?php echo $m['MovieID']; ?></td>
                    <td><?php echo htmlspecialchars($m['title']); ?></td>
                    <td><?php echo htmlspecialchars($m['genre']); ?></td>
                    <td><?php echo $m['duration']; ?>'</td>
                    <td><?php echo htmlspecialchars($m['rating']); ?></td>
                    <td><span class="badge bg-<?php echo $m['status']=='Now Showing'?'success':'secondary'; ?>"><?php echo $m['status']; ?></span></td>
                    <td>
                        <a href="movie.php?edit=<?php echo $m['MovieID']; ?>#formMovie" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="movie.php?delete=<?php echo $m['MovieID']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Hapus film ini? Semua jadwal terkait juga akan terhapus.');">Hapus</a>
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
