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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebBro Shop</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div class="header-left">
            <h1>🛍️ WebBro Shop</h1>
            <p class="header-subtitle">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        </div>
        <div class="header-right">
            <a href="profile.php" class="nav-link">👤 Profile</a>
            <a href="wishlist.php" class="nav-link">❤️ Wishlist</a>
            <span class="cart-badge-wrap">
                <button class="nav-btn" onclick="toggleCartPanel()">🛒 Cart <span id="cart-count" class="cart-badge">0</span></button>
            </span>
            <a href="logout.php" class="nav-link logout-link">Logout</a>
        </div>
    </header>

    <div class="search-bar-wrap">
        <input type="text" id="search-input" placeholder="🔍 Search products…" oninput="filterProducts()" />
        <select id="category-filter" onchange="filterProducts()">
            <option value="">All Categories</option>
            <option value="Foods">Foods</option>
            <option value="Cloths">Cloths</option>
            <option value="Electrics">Electrics</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <div class="categories" id="products-container">

        <div class="category" data-name="Foods" data-category="Foods">
            <img src="images/product1.jpg" alt="Foods" class="product-image" onerror="this.style.display='none'">
            <h3>Foods</h3>
            <p class="product-desc">Fresh groceries &amp; snacks delivered to your door.</p>
            <p class="product-price">₹199</p>
            <div class="product-actions">
                <button onclick="addToCart('Foods', 199)">🛒 Add to Cart</button>
                <button class="btn-wishlist" onclick="addToWishlist('Foods', 199)">❤️ Wishlist</button>
            </div>
        </div>

        <div class="category" data-name="Cloths" data-category="Cloths">
            <img src="images/product2.jpg" alt="Cloths" class="product-image" onerror="this.style.display='none'">
            <h3>Cloths</h3>
            <p class="product-desc">Trendy fashion for all seasons &amp; styles.</p>
            <p class="product-price">₹499</p>
            <div class="product-actions">
                <button onclick="addToCart('Cloths', 499)">🛒 Add to Cart</button>
                <button class="btn-wishlist" onclick="addToWishlist('Cloths', 499)">❤️ Wishlist</button>
            </div>
        </div>

        <div class="category" data-name="Electrics" data-category="Electrics">
            <img src="images/product3.jpg" alt="Electrics" class="product-image" onerror="this.style.display='none'">
            <h3>Electrics</h3>
            <p class="product-desc">Latest gadgets &amp; electronics at great prices.</p>
            <p class="product-price">₹2,999</p>
            <div class="product-actions">
                <button onclick="addToCart('Electrics', 2999)">🛒 Add to Cart</button>
                <button class="btn-wishlist" onclick="addToWishlist('Electrics', 2999)">❤️ Wishlist</button>
            </div>
        </div>

        <div class="category" data-name="Other items" data-category="Other">
            <img src="images/product4.jpg" alt="Other items" class="product-image" onerror="this.style.display='none'">
            <h3>Other Items</h3>
            <p class="product-desc">Household essentials &amp; everyday must-haves.</p>
            <p class="product-price">₹149</p>
            <div class="product-actions">
                <button onclick="addToCart('Other items', 149)">🛒 Add to Cart</button>
                <button class="btn-wishlist" onclick="addToWishlist('Other items', 149)">❤️ Wishlist</button>
            </div>
        </div>

    </div>

    <p id="no-results" class="no-results" style="display:none;">No products found.</p>

    <!-- Cart Panel -->
    <div id="cart-panel" class="cart-panel hidden">
        <div class="cart-panel-header">
            <h3>🛒 Your Cart</h3>
            <button class="cart-close-btn" onclick="toggleCartPanel()">✕</button>
        </div>
        <div id="cart-items"></div>
        <div class="cart-footer">
            <p id="cart-total" class="cart-total"></p>
            <div class="cart-actions">
                <button onclick="checkout()">✅ Checkout</button>
                <button onclick="clearCart()" class="btn-secondary">🗑️ Clear</button>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="toast hidden"></div>

    <script src="script.js"></script>
</body>

</html>