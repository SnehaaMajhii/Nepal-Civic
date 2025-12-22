<?php
$conn = mysqli_connect("localhost", "root", "", "nepal_civic");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
session_start();
?>