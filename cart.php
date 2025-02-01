<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $item = $_POST['item'];

    if ($action === 'add') {
        $_SESSION['cart'][] = $item;
    } elseif ($action === 'remove') {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($cartItem) use ($item) {
            return $cartItem !== $item;
        });
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
    }
}

echo json_encode($_SESSION['cart']);
?>
