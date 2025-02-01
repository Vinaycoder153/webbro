document.addEventListener("DOMContentLoaded", loadCart);

function addToCart(item) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&item=${item}`
    })
    .then(response => response.json())
    .then(updateCart);
}

function loadCart() {
    fetch('cart.php')
    .then(response => response.json())
    .then(updateCart);
}

function updateCart(cart) {
    document.getElementById('cart-items').innerHTML = cart.map(item =>
        `<div>${item} <button onclick="removeFromCart('${item}')">Remove</button></div>`
    ).join('');
}

function removeFromCart(item) {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&item=${item}`
    })
    .then(response => response.json())
    .then(updateCart);
}

function checkout() {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=checkout'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Checkout successful!');
            loadCart();
        } else {
            alert('Checkout failed!');
        }
    });
}

function clearCart() {
    fetch('cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=clear'
    })
    .then(response => response.json())
    .then(updateCart);
}

function showCart() {
    loadCart();
}