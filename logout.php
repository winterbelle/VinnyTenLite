<?php
session_start();

// Kill the old session
session_unset();
session_destroy();

// Start a fresh session ONLY for the logout message
session_start();
$_SESSION['logout_message'] = "You have been logged out.";

header("Location: home.php");
exit;
?>
