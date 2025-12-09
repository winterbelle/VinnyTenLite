<?php
session_start();
require_once 'config.php';
require_once 'stripe_config.php';
require_once __DIR__ . '/vendor/autoload.php';


$conn = new mysqli("localhost", "root", "", "VTR");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Get Stripe session ID from URL
$sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if (empty($sessionId)) {
    http_response_code(400);
    echo "Invalid success parameters: missing session_id.";
    exit;
}

// Retrieve session from Stripe
$session = \Stripe\Checkout\Session::retrieve($sessionId);

// Get order from Stripe metadata
$orderId = isset($session->metadata->order_id) ? (int)$session->metadata->order_id : 0;

if ($orderId <= 0) {
    http_response_code(400);
    echo "Invalid success parameters: missing order_id.";
    exit;
}

// If paid, update order in database
if ($session->payment_status === 'paid') {

    $stmt = $conn->prepare(
        "UPDATE orders 
         SET status = 'completed'
         WHERE order_id = ? AND stripe_session_id = ?"
    );
    $stmt->bind_param("is", $orderId, $sessionId);
    $stmt->execute();
    $stmt->close();

    // Clear cart
    unset($_SESSION['cart']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful | Vinny Ten Racing</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <style>
        .success-wrapper {
            max-width: 700px;
            margin: 40px auto 60px;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 12px;
            text-align: center;
        }

        .success-wrapper h1 {
            color: #013783;
            margin-bottom: 10px;
        }

        .success-wrapper p {
            margin: 8px 0;
            font-size: 1rem;
        }

        .order-number {
            font-weight: bold;
            color: #111827;
        }

        .success-actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .shop-again,
        .home {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            transition: 0.25s ease;
        }

        .shop-again {
            background: linear-gradient(90deg, #013783, #0056b3);
            color: #fff;
        }

        .shop-again:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #0056b3, #013783);
        }
        
        .home {
            border: 2px solid #013783;
            color: #013783;
            background: #ffffff;
        }

        .home:hover {
            background: #e5efff;
            transform: scale(1.05);
        }

    </style>
</head>
<body>

<?php include "header.php"; ?>

<div class="success-wrapper">
    <h1>Payment Successful 🎉</h1>
    <p>Thank you for your purchase.</p>
    <p>Your order number is
        <span class="order-number">#<?= htmlspecialchars($orderId) ?></span>.
    </p>
    <p>You’ll receive a receipt from Stripe at the email you used during payment.</p>

    <div class="success-actions">
        <a href="shop.php" class="shop-again">← Shop Again</a>
        <a href="home.php" class="home">Back to Home</a>
    </div>
</div>

<?php include "footer.php"; ?>

</body>
</html>
