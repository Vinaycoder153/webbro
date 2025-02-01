<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <a href="logout.php">Logout</a>
    </header>

    <div class="categories">
        <div class="category">
            <img src="images/product1.jpg" alt="Product 1" class="product-image">
            <h3>Foods</h3>
            <button onclick="addToCart('Product 1')"><a href="#">Add to Cart</a></button>
        </div>
        <div class="category">
            <img src="images/product2.jpg" alt="Product 2" class="product-image">
            <h3>Cloths</h3>
            <button onclick="addToCart('Product 2')"><a href="#">Add to Cart</a></button>
        </div>
        <div class="category">
            <img src="images/product3.jpg" alt="Product 3" class="product-image">
            <h3>Electrics</h3>
            <button onclick="addToCart('Product 3')"><a href="#">Add to Cart</a></button>
        </div>
        <div class="category">
            <img src="images/product4.jpg" alt="Product 4" class="product-image">
            <h3>Other items</h3>
            <button onclick="addToCart('Product 4')"><a href="#">Add to Cart</a></button>
        </div>
    </div>
    <h2>Shopping Cart</h2>
    <button onclick="checkout()">Checkout</button>
    <button onclick="clearCart()">Clear Cart</button>
    <button onclick="showCart()">Show Cart</button>


    <div id="cart-items"></div>
    <script src="script.js"></script>
</body>

</html>