<?php
// Include header
require_once('header.php');

// Determine which page to show
$page = isset($_GET['page']) ? $_GET['page'] : 'products';

// Include appropriate file depending on requested page
switch ($page) {
    case 'products':
        require_once('products.php');
        break;
    case 'cart':
        require_once('cart.php');
        break;
    case 'about':
        require_once('about.php');
        break;
    default:
        require_once('page404.php');
        break;
}

// Include footer
require_once('footer.php');
?>
