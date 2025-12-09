<?php
session_start();
require_once 'config.php';
require_once 'stripe_config.php';
require_once __DIR__ . '/vendor/autoload.php';


$conn = new mysqli("localhost", "root", "", "VTR");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    http_response_code(400);
    header("Location: cart.php");
    exit;
}


$cart = $_SESSION['cart'] ?? [];
if (empty($cart) || !is_array($cart)) {
    header("Location: cart.php");
    exit;
}


$productIds = array_map('intval', array_keys($cart));
if (empty($productIds)) {
    header("Location: cart.php");
    exit;
}

$idList = implode(',', $productIds);


$sql = "SELECT productId, name, price
        FROM products
        WHERE productId IN ($idList)";
$result = $conn->query($sql);

if (!$result) {
    error_log('DB error in create_checkout_session: ' . $conn->error);
    http_response_code(500);
    echo 'Database error';
    exit;
}

$line_items = [];
$cart_total = 0.0;
$items_for_db = [];

while ($row = $result->fetch_assoc()) {
    $pid = (int)$row['productId'];
    $qty = isset($cart[$pid]) ? (int)$cart[$pid] : 0;
    if ($qty < 1) continue;

    $price = (float)$row['price'];
    $cart_total += $price * $qty;

    // For Stripe
    $line_items[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $row['name'],
            ],
            'unit_amount' => (int)round($price * 100),
        ],
        'quantity' => $qty,
    ];

    // For DB
    $items_for_db[] = [
        'productId'  => $pid,
        'name'       => $row['name'],
        'price'      => $price,
        'qty'        => $qty,
        'line_total' => $price * $qty,
    ];
}

if (empty($line_items)) {
    header("Location: cart.php");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO orders (email, guest_name, total_amount, status, stripe_session_id)
     VALUES (NULL, NULL, ?, 'pending', NULL)"
);
$stmt->bind_param('d', $cart_total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();


$itemStmt = $conn->prepare(
    "INSERT INTO order_items (order_id, productId, name, price, qty, line_total)
     VALUES (?, ?, ?, ?, ?, ?)"
);

foreach ($items_for_db as $it) {
    $oid  = $order_id;
    $pid  = $it['productId'];
    $name = $it['name'];
    $price = $it['price'];
    $qty   = $it['qty'];
    $line  = $it['line_total'];

    $itemStmt->bind_param('iisdid', $oid, $pid, $name, $price, $qty, $line);
    $itemStmt->execute();
}
$itemStmt->close();


$session = \Stripe\Checkout\Session::create([
    'mode' => 'payment',
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'success_url' => 'http://localhost/Belle-mac272/Vinny-Ten-Lite/checkout_success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'  => 'http://localhost/Belle-mac272/Vinny-Ten-Lite/cart.php',
    'metadata' => [
        'order_id' => $order_id,
    ],
]);


$session_id = $session->id;

$update = $conn->prepare(
    "UPDATE orders SET stripe_session_id = ? WHERE order_id = ?"
);
$update->bind_param('si', $session_id, $order_id);
$update->execute();
$update->close();


header('Location: ' . $session->url);
exit;
