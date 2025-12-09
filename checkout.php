<?php
session_start();
require_once 'config.php';

// DB connection (your DB)
$conn = new mysqli("localhost", "root", "", "VTR");

if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Get cart from session
$cart = $_SESSION['cart'] ?? [];

if (empty($cart) || !is_array($cart)) {
    header("Location: cart.php");
    exit;
}

// Build list of product IDs
$ids = array_map('intval', array_keys($cart));
$idList = implode(',', $ids);

// Fetch products from YOUR products table
$sql = "SELECT productId, name, price 
        FROM products
        WHERE productId IN ($idList)";
$res = $conn->query($sql);

$items = [];
$total = 0.0;

while ($row = $res->fetch_assoc()) {
    $pid = (int)$row['productId'];
    $qty = isset($cart[$pid]) ? (int)$cart[$pid] : 0;
    if ($qty <= 0) continue;

    $price     = (float)$row['price'];
    $lineTotal = $price * $qty;
    $total    += $lineTotal;

    $items[] = [
        'productId'  => $pid,
        'name'       => $row['name'],
        'price'      => $price,
        'qty'        => $qty,
        'line_total' => $lineTotal,
    ];
}

// If somehow nothing valid, bounce back
if (empty($items)) {
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Vinny Ten Racing</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <style>
        .checkout-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px 40px;
        }

        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 20px;
        }

        .checkout-header h1 {
            margin: 0;
            color: #013783;
        }

        .checkout-header a {
            font-size: 0.95rem;
            text-decoration: none;
            color: #013783;
            font-weight: bold;
        }

        .checkout-header a:hover {
            text-decoration: underline;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-table th {
            background-color: #f3f4f6;
            text-align: left;
        }

        .cart-table tfoot th {
            background-color: #f9fafb;
        }

        .checkout-actions {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }

        .pay-button {
            background: linear-gradient(90deg, #013783, #0056b3);
            color: #fff;
            padding: 12px 22px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.25s ease;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        }

        .pay-button:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #0056b3, #013783);
        }


        .btn-secondary {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid #013783;
            color: #013783;
            background: #ffffff;
        }

        .btn-secondary:hover {
            background: #e5efff;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>

<div class="checkout-wrapper">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <a href="cart.php">← Back to Cart</a>
    </div>

    <table class="cart-table">
        <thead>
        <tr>
            <th>Product</th>
            <th style="text-align:right;">Price</th>
            <th style="text-align:center;">Qty</th>
            <th style="text-align:right;">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td style="text-align:right;">
                    $<?= number_format($it['price'], 2) ?>
                </td>
                <td style="text-align:center;">
                    <?= (int)$it['qty'] ?>
                </td>
                <td style="text-align:right;">
                    $<?= number_format($it['line_total'], 2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3" style="text-align:right;">Subtotal:</th>
            <th style="text-align:right;">
                $<?= number_format($total, 2) ?>
            </th>
        </tr>
        </tfoot>
    </table>

    <div class="checkout-actions">
        <a href="shop.php" class="btn-secondary">← Keep Shopping</a>

        <form method="post" action="create_checkout_session.php">
            <?php csrf_field(); ?>
            <button type="submit" class="pay-button">
                Pay Securely with Card
            </button>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>
</body>
</html>
