<?php
session_start();

// Clear all technician session variables
unset($_SESSION['is_tech_login']);
unset($_SESSION['tEmail']);
unset($_SESSION['tId']);
unset($_SESSION['tName']);

// Destroy session
session_destroy();

// Redirect to login page
header("Location: TechnicianLogin.php");
exit();
?>