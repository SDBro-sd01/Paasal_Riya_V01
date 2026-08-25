<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session completely
session_unset();
session_destroy();

// Destroy 'abc' cookie
if (isset($_COOKIE['abc'])) {
    // Set the cookie expiration to past date
    setcookie('abc', '', time() - 3600, '/');
}

// Redirect user to login page or homepage
header("Location: index.php"); // ඔබ කැමති page එකට redirect කරන්න
exit;
?>