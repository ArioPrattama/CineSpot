<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CineSpot Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body{ background:#f5f5f5; font-family:'Poppins',sans-serif; }
.admin-sidebar{
    min-height:100vh;
    background:#1e1e2d;
}
.admin-sidebar a{
    display:block;
    padding:12px 20px;
    color:#c8c9cc;
    text-decoration:none;
    font-size:14px;
}
.admin-sidebar a.active,.admin-sidebar a:hover{
    background:#0d6efd;
    color:white;
}
.admin-sidebar .brand{
    color:white;
    font-weight:bold;
    padding:20px;
    font-size:18px;
    border-bottom:1px solid #333;
}
.admin-content{
    padding:25px;
}
.stat-card{
    border-radius:15px;
    border:none;
    box-shadow:0px 5px 20px rgba(0,0,0,.06);
}
</style>

</head>
<body>

<div class="d-flex">

<div class="admin-sidebar" style="width:220px;">

    <div class="brand">🎬 CineSpot Admin</div>

    <?php $current = basename($_SERVER['PHP_SELF']); ?>

    <a href="index.php" class="<?php echo $current=='index.php'?'active':''; ?>">📊 Dashboard</a>
    <a href="movie.php" class="<?php echo $current=='movie.php'?'active':''; ?>">🎬 Movie</a>
    <a href="schedule.php" class="<?php echo $current=='schedule.php'?'active':''; ?>">🗓 Schedule</a>
    <a href="cinema.php" class="<?php echo $current=='cinema.php'?'active':''; ?>">🏢 Cinema</a>
    <a href="studio.php" class="<?php echo $current=='studio.php'?'active':''; ?>">🎦 Studio</a>
    <a href="promo.php" class="<?php echo $current=='promo.php'?'active':''; ?>">🎁 Promo</a>
    <a href="fnb.php" class="<?php echo $current=='fnb.php'?'active':''; ?>">🍿 F&amp;B</a>
    <a href="../pages/profile/index.php">↩ Kembali ke App</a>
    <a href="../logout.php">🚪 Logout</a>

</div>

<div class="admin-content flex-grow-1">
