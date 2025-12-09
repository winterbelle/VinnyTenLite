<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    http_response_code(400);
    header("Location: shop.php");
    exit;
}

$productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;
$qty       = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

if ($productId <= 0 || $qty <= 0) {
    header("Location: shop.php");
    exit;
}

// Initialize cart session
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId] = 0;
}

$_SESSION['cart'][$productId] += $qty;

if ($_SESSION['cart'][$productId] > 10) {
    $_SESSION['cart'][$productId] = 10;
}

header("Location: cart.php");
exit;
