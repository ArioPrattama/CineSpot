<?php
session_start();

include "../../config/database.php";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM `User` WHERE email='$email'");

    if(mysqli_num_rows($query) == 1){

        $user = mysqli_fetch_assoc($query);

        if(password_verify($password,$user['password'])){

            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['fullName'] = $user['fullName'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../../index.php");
            exit;

        }else{

            $error = "Password salah!";

        }

    }else{

        $error = "Email tidak ditemukan!";

    }

}

include "../../includes/header.php";
include "../../includes/navbar.php";
?>

<div class="container mt-4">

<h3 class="page-title">Login</h3>

<?php
if(isset($error)){
    echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">

<div class="mb-3">
<label>Email</label>
<input
type="email"
name="email"
class="form-control"
required>
</div>

<div class="mb-3">
<label>Password</label>
<input
type="password"
name="password"
class="form-control"
required>
</div>

<button
type="submit"
name="login"
class="btn btn-primary w-100">

Login

</button>

<div class="text-center mt-3">

Belum punya akun?

<a href="register.php">

Daftar

</a>

</div>

</form>

</div>

<?php
include "../../includes/bottom_nav.php";
include "../../includes/footer.php";
?>