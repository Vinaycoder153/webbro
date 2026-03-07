<?php
session_start();
if (!isset($_SESSION['username'])) {
    /* For AJAX calls return JSON; otherwise redirect */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Not logged in']);
        exit;
    }
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

/* ── AJAX / POST handler ──────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $item   = $_POST['item']   ?? '';
    $price  = isset($_POST['price']) ? (int)$_POST['price'] : 0;

    if ($action === 'add' && $item !== '') {
        if (!isset($_SESSION['wishlist'][$item])) {
            $_SESSION['wishlist'][$item] = ['price' => $price];
            echo json_encode(['message' => '"' . htmlspecialchars($item) . '" added to wishlist!']);
        } else {
            echo json_encode(['message' => '"' . htmlspecialchars($item) . '" is already in your wishlist.']);
        }
    } elseif ($action === 'remove' && $item !== '') {
        unset($_SESSION['wishlist'][$item]);
        echo json_encode(['removed' => true]);
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

$wishlist = $_SESSION['wishlist'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist – WebBro Shop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .wishlist-container {
            max-width: 680px;
            margin: 30px auto;
            padding: 0 20px 40px;
        }
        .wishlist-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .wishlist-card h2 {
            margin-top: 0;
            color: #333;
            font-size: 1.2em;
            border-bottom: 2px solid #e0436f;
            padding-bottom: 8px;
        }
        .wishlist-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            gap: 10px;
        }
        .wishlist-item:last-child { border-bottom: none; }
        .wishlist-item-name { font-weight: bold; flex: 1; }
        .wishlist-item-price { color: #ff6347; font-weight: bold; min-width: 80px; text-align: right; }
        .wishlist-actions { display: flex; gap: 8px; }
        .no-wishlist { color: #aaa; text-align: center; padding: 30px 0; }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1>🛍️ WebBro Shop</h1>
            <p class="header-subtitle">My Wishlist</p>
        </div>
        <div class="header-right">
            <a href="index.php" class="nav-link">🏠 Shop</a>
            <a href="profile.php" class="nav-link">👤 Profile</a>
            <a href="logout.php" class="nav-link logout-link">Logout</a>
        </div>
    </header>

    <div class="wishlist-container">
        <div class="wishlist-card">
            <h2>❤️ My Wishlist</h2>

            <?php if (empty($wishlist)): ?>
                <p class="no-wishlist">Your wishlist is empty. Browse the shop and add items you love!</p>
                <div style="text-align:center;margin-top:16px;">
                    <a href="index.php" style="display:inline-block;">
                        <button>🏠 Go to Shop</button>
                    </a>
                </div>
            <?php else: ?>
                <div id="wishlist-items">
                <?php foreach ($wishlist as $name => $data): ?>
                    <div class="wishlist-item" id="wish-<?= urlencode($name) ?>">
                        <span class="wishlist-item-name"><?= htmlspecialchars($name) ?></span>
                        <span class="wishlist-item-price">₹<?= number_format($data['price']) ?></span>
                        <div class="wishlist-actions">
                            <button onclick="moveToCart(this);" data-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>" data-price="<?= (int)$data['price'] ?>">🛒 Add to Cart</button>
                            <button class="btn-secondary" onclick="removeFromWishlist(this);" data-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>">🗑️ Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="toast" class="toast hidden"></div>

    <script>
    function removeFromWishlist(btn) {
        const item = btn.dataset.name;
        fetch('wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=remove&item=${encodeURIComponent(item)}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.removed) {
                const el = document.getElementById('wish-' + encodeURIComponent(item));
                if (el) el.remove();
                showToast(`"${item}" removed from wishlist.`);
                const container = document.getElementById('wishlist-items');
                if (container && container.children.length === 0) {
                    container.innerHTML = '<p class="no-wishlist">Your wishlist is empty.</p>';
                }
            }
        });
    }

    function moveToCart(btn) {
        const item  = btn.dataset.name;
        const price = btn.dataset.price;
        fetch('cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add&item=${encodeURIComponent(item)}&price=${price}`
        })
        .then(r => r.json())
        .then(() => {
            const removeBtn = btn.closest('.wishlist-item').querySelector('.btn-secondary');
            removeFromWishlist(removeBtn);
            showToast(`"${item}" moved to cart!`);
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.classList.remove('hidden');
        clearTimeout(showToast._t);
        showToast._t = setTimeout(() => toast.classList.add('hidden'), 3000);
    }
    </script>
</body>
</html>
