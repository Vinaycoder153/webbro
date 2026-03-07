document.addEventListener("DOMContentLoaded", loadCart);

/* ── Cart ── */

function addToCart(item, price) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&item=${encodeURIComponent(item)}&price=${price}`
    })
    .then(r => r.json())
    .then(cart => { updateCart(cart); showToast(`"${item}" added to cart!`); });
}

function loadCart() {
    fetch('cart.php')
    .then(r => r.json())
    .then(updateCart);
}

function updateCart(cart) {
    const container = document.getElementById('cart-items');
    const badge = document.getElementById('cart-count');

    if (!Array.isArray(cart) || cart.length === 0) {
        container.innerHTML = '<p class="empty-cart">Your cart is empty.</p>';
        badge.textContent = '0';
        document.getElementById('cart-total').textContent = '';
        return;
    }

    let total = 0;
    let totalQty = 0;
    container.innerHTML = cart.map(item => {
        const lineTotal = item.price * item.qty;
        total += lineTotal;
        totalQty += item.qty;
        return `<div class="cart-item">
            <span class="cart-item-name">${escHtml(item.name)}</span>
            <div class="cart-qty-controls">
                <button class="qty-btn" onclick="decreaseQty('${escJs(item.name)}')">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="addToCart('${escJs(item.name)}', ${item.price})">+</button>
            </div>
            <span class="cart-item-price">₹${lineTotal.toLocaleString('en-IN')}</span>
            <button class="remove-btn" onclick="removeFromCart('${escJs(item.name)}')">✕</button>
        </div>`;
    }).join('');

    badge.textContent = totalQty;
    document.getElementById('cart-total').textContent = `Total: ₹${total.toLocaleString('en-IN')}`;
}

function removeFromCart(item) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&item=${encodeURIComponent(item)}`
    })
    .then(r => r.json())
    .then(updateCart);
}

function decreaseQty(item) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=decrease&item=${encodeURIComponent(item)}`
    })
    .then(r => r.json())
    .then(updateCart);
}

function checkout() {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=checkout'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('✅ ' + data.message);
            loadCart();
            toggleCartPanel();
        } else {
            showToast('⚠️ ' + data.message);
        }
    });
}

function clearCart() {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=clear'
    })
    .then(r => r.json())
    .then(updateCart);
}

function toggleCartPanel() {
    const panel = document.getElementById('cart-panel');
    panel.classList.toggle('hidden');
}

/* ── Search / Filter ── */

function filterProducts() {
    const query = (document.getElementById('search-input').value || '').toLowerCase();
    const category = document.getElementById('category-filter').value;
    const cards = document.querySelectorAll('#products-container .category');
    let visible = 0;

    cards.forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        const cat  = (card.dataset.category || '');
        const matchQuery    = name.includes(query);
        const matchCategory = !category || cat === category;
        if (matchQuery && matchCategory) {
            card.style.display = '';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('no-results').style.display = visible === 0 ? 'block' : 'none';
}

/* ── Wishlist ── */

function addToWishlist(item, price) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&item=${encodeURIComponent(item)}&price=${price}`
    })
    .then(r => r.json())
    .then(data => showToast(data.message || `"${item}" added to wishlist!`));
}

/* ── Utilities ── */

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escJs(str) {
    return String(str)
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'");
}

function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.remove('hidden');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toast.classList.add('hidden'), 3000);
}