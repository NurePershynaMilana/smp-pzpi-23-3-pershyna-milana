<?php
// Start session for cart functionality
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .header {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            margin-bottom: 20px;
        }
        
        .container {
            width: 80%;
            margin: 0 auto;
            overflow: hidden;
        }
        
        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .navigation a {
            text-decoration: none;
            color: #333;
            padding: 10px 15px;
            display: inline-block;
        }
        
        .content {
            background: #fff;
            padding: 20px;
            min-height: 500px;
            margin-bottom: 20px;
        }
        
        .footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        
        .footer a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }
        
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        .product-item {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            width: 100%;
            background: #fff;
        }
        
        .product-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .count-input {
            width: 50px;
            padding: 5px;
            margin: 0 10px;
        }
        
        .button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cart-table th, .cart-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .cart-table th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="navigation">
                <div>
                    <a href="main.php">🏠 Home</a> |
                    <a href="main.php?page=products">📋 Products</a> |
                    <a href="main.php?page=cart">🛒 Cart</a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        | <a href="main.php?page=profile">👤 Profile</a>
                        | <a href="main.php?page=logout">🚪 Logout</a>
                    <?php else: ?>
                        | <a href="main.php?page=login">🔑 Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="content">
