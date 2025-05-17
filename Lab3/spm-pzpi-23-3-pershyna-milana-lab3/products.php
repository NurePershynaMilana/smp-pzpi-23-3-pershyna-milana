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

// Processing adding product to cart
if (isset($_POST['addIdInput'])) {
    $id = (int)$_POST['addIdInput'];
    $addCount = isset($_POST['addCountInput']) && $_POST['addCountInput'] > 0 
        ? (int)$_POST['addCountInput'] 
        : 1;
    
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product is already in cart
    $itemExists = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            $_SESSION['cart'][$key]['count'] += $addCount;
            $itemExists = true;
            break;
        }
    }
    
    // If product is not in cart, add it
    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'count' => $addCount
        ];
    }
    
    // Redirect to cart page
    header('Location: main.php?page=cart');
    exit;
}
?>

<h2>Our Products</h2>

<div class="product-list">
    <?php foreach ($products as $id => $product): ?>
    <div class="product-item">
        <div class="product-form">
            <div>
                <h3><?= $product['title'] ?></h3>
                <p><?= $product['description'] ?></p>
                <strong>Price: $<?= number_format($product['price'], 2) ?></strong>
            </div>
            
            <form method="POST" action="main.php?page=products" class="add-to-cart-form">
                <input type="hidden" name="addIdInput" value="<?= $id ?>">
                <input type="text" class="count-input" name="addCountInput" placeholder="1">
                <input class="button" type="submit" value="Buy">
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
