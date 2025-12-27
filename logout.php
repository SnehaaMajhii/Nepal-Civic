<?php
// logout.php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page with a success message
header("Location: login.php?msg=loggedout");
exit();
?>