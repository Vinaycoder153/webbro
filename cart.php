<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $item   = $_POST['item'] ?? '';
    $price  = isset($_POST['price']) ? (int)$_POST['price'] : 0;

    if ($action === 'add' && $item !== '') {
        if (isset($_SESSION['cart'][$item])) {
            $_SESSION['cart'][$item]['qty']++;
        } else {
            $_SESSION['cart'][$item] = ['price' => $price, 'qty' => 1];
        }
    } elseif ($action === 'remove' && $item !== '') {
        unset($_SESSION['cart'][$item]);
    } elseif ($action === 'decrease' && $item !== '') {
        if (isset($_SESSION['cart'][$item])) {
            $_SESSION['cart'][$item]['qty']--;
            if ($_SESSION['cart'][$item]['qty'] <= 0) {
                unset($_SESSION['cart'][$item]);
            }
        }
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
    } elseif ($action === 'checkout') {
        if (!empty($_SESSION['cart'])) {
            // Save order to session history
            if (!isset($_SESSION['orders'])) {
                $_SESSION['orders'] = [];
            }
            $_SESSION['orders'][] = [
                'items' => $_SESSION['cart'],
                'date'  => date('Y-m-d H:i:s'),
                'total' => array_sum(array_map(function($v) { return $v['price'] * $v['qty']; }, $_SESSION['cart'])),
            ];
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
            exit;
        }
    }
}

echo json_encode(array_values(array_map(function ($name, $data) {
    return ['name' => $name, 'price' => $data['price'], 'qty' => $data['qty']];
}, array_keys($_SESSION['cart']), array_values($_SESSION['cart']))));
?>
