<?php

include "../../config/database.php";

$success = false;

if(isset($_POST['register'])){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek email sudah digunakan atau belum
    $check = mysqli_query($conn,"SELECT * FROM `User` WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){

        $error = "Email sudah terdaftar!";

    }else{

        $sql = "INSERT INTO `User`
        (fullName,email,password,phone,loyaltyPoints,role)
        VALUES
        ('$fullname','$email','$password','$phone',0,'user')";

        if(mysqli_query($conn,$sql)){

            $success = true;

        }else{

            $error = "Registrasi gagal!";
        }

    }

}

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-4">

<h3 class="page-title">Register</h3>

<?php
if(isset($error)){
    echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">

<div class="mb-3">
<label>Nama Lengkap</label>
<input type="text" class="form-control" name="fullname" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" class="form-control" name="email" required>
</div>

<div class="mb-3">
<label>No HP</label>
<input type="text" class="form-control" name="phone" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" class="form-control" name="password" required>
</div>

<button type="submit" name="register" class="btn btn-primary w-100">
Register
</button>

<div class="text-center mt-3">
Sudah punya akun?
<a href="login.php">Login</a>
</div>

</form>

</div>

<!-- Modal Popup Registrasi Berhasil -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <div style="font-size:60px;">✅</div>
                <h5 class="mt-3 mb-2">Registrasi Berhasil!</h5>
                <p class="text-muted mb-4">Akun kamu sudah berhasil dibuat. Silakan login untuk mulai pesan tiket bioskop favoritmu.</p>
                <a href="login.php" class="btn btn-primary w-100">Login Sekarang</a>
            </div>
        </div>
    </div>
</div>

<?php if($success){ ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
});
</script>
<?php } ?>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>