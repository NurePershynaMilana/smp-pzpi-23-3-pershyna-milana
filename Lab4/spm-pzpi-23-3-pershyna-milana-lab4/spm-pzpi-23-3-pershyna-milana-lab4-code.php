<?php
// Include header
require_once('header.php');

// Determine which page to show
$page = isset($_GET['page']) ? $_GET['page'] : 'products';

// Check if access is restricted
$restricted_pages = ['profile', 'cart'];
if (in_array($page, $restricted_pages) && (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true)) {
    // Show login required message
    ?>
    <div style="text-align: center; padding: 50px 0;">
        <h2>Please Login first</h2>
        <p><a href="main.php?page=login">Go to login page</a></p>
    </div>
    <?php
} else {
    // Include appropriate file depending on requested page
    switch ($page) {
        case 'products':
            require_once('products.php');
            break;
        case 'cart':
            require_once('cart.php');
            break;
        case 'login':
            require_once('login.php');
            break;
        case 'logout':
            require_once('logout.php');
            break;
        case 'profile':
            require_once('profile.php');
            break;
        case 'about':
            require_once('about.php');
            break;
        default:
            require_once('page404.php');
            break;
    }
}

// Include footer
require_once('footer.php');
?>
