<?php
// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to redirect to login page if not logged in
function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header('Location: main.php?page=login');
        exit;
    }
}
?>
