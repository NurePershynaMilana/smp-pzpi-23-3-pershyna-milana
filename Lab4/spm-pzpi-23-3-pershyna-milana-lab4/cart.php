<?php
// Products array (simulating database)
$products = [
    1 => [
        'id' => 1,
        'title' => 'Cola',
        'description' => 'Refreshing carbonated drink',
        'price' => 1.50
    ],
    2 => [
        'id' => 2,
        'title' => 'Fanta',
        'description' => 'Orange flavored carbonated drink',
        'price' => 1.50
    ],
    3 => [
        'id' => 3,
        'title' => 'Sprite',
        'description' => 'Clear lemon-lime flavored carbonated drink',
        'price' => 1.50
    ],
    4 => [
        'id' => 4,
        'title' => 'Nuts',
        'description' => 'Assorted nuts mix',
        'price' => 3.00
    ]
];

// Processing removal of product from cart
if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $removeId) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
    }
    
    // Redirect to cart page
    header('Location: main.php?page=cart');
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    echo '<h2>Your cart is empty</h2>';
    echo '<p><a href="main.php?page=products">Go shopping</a></p>';
} else {
    // Display cart contents
    echo '<h2>Cart</h2>';
    ?>
    <table class="cart-table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Sum</th>
            <th>Actions</th>
        </tr>
        <?php
        $totalSum = 0;
        
        foreach ($_SESSION['cart'] as $item) {
            $id = $item['id'];
            $count = $item['count'];
            
            // Check if product exists in our "catalog"
            if (isset($products[$id])) {
                $product = $products[$id];
                $sum = $product['price'] * $count;
                $totalSum += $sum;
                ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><?= $product['title'] ?></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                    <td><?= $count ?></td>
                    <td>$<?= number_format($sum, 2) ?></td>
                    <td>
                        <a href="main.php?page=cart&remove=<?= $id ?>">🗑️</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td colspan="4"><strong>Total:</strong></td>
            <td colspan="2"><strong>$<?= number_format($totalSum, 2) ?></strong></td>
        </tr>
    </table>
    
    <div style="margin-top: 20px; text-align: right;">
        <a href="main.php?page=products" class="button" style="background-color: #888; margin-right: 10px;">Continue Shopping</a>
        <button class="button">Checkout</button>
    </div>
    <?php
}
?>
