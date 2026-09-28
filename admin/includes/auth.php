<?php
// Panggil file ini di baris paling atas SETIAP halaman di folder admin/
// Wajib dipanggil SEBELUM session_start() dipanggil di tempat lain pada file yang sama.

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['UserID'])){
    header("Location: ../login.php");
    exit;
}

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit;
}
