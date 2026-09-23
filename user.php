<?php
session_start();

// Security Guard: Just check for the wristband
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
<a href="logout.php">Log Out</a>