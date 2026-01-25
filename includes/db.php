<?php
/* =========================
   SESSION START (SAFE)
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   DATABASE CONNECTION
========================= */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nepalcivic";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
