<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR");

// Load cart
$cart = $_SESSION['cart'] ?? [];
$productRows = [];
$total = 0.0;

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $idList = implode(',', $ids);

    $sql = "SELECT productId, name, price, image 
            FROM products 
            WHERE productId IN ($idList)";
    $res = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($res)) {
        $pid = (int)$row['productId'];
        $qty = (int)($cart[$pid] ?? 0);

        if ($qty <= 0) continue;

        $row['qty'] = $qty;
        $row['line_total'] = $qty * (float)$row['price'];
        $total += $row['line_total'];

        $productRows[] = $row;
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <title>Your Cart | Vinny Ten Racing</title>

    <style>
        .cart-container {
            max-width: 1000px;
            margin: 40px auto;
            background: #f5f5f5;
            padding: 30px;
            border-radius: 12px;
        }

        .cart-header {
            font-size: 32px;
            font-weight: bold;
            color: #013783;
            margin-bottom: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .cart-table th {
            background: #013783;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .cart-table td {
            background: white;
            padding: 14px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        .cart-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .subtotal-row th {
            font-size: 20px;
            text-align: right;
            background: #013783;
        }

        .subtotal-val {
            font-size: 20px;
            font-weight: bold;
            color: #d50000;
            text-align: right;
        }

        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-primary {
            background: #013783;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #0050c9;
        }

        .btn-secondary {
            background: #d50000;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #a70000;
        }

        .link-button {
            background: none;
            border: none;
            color: #d50000;
            cursor: pointer;
            text-decoration: underline;
        }

        .link-button:hover {
            color: #900;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="cart-container">
    <div class="cart-header">Your Cart</div>

    <?php if (empty($productRows)): ?>
        
        <div class="empty-cart">
            <h2>Your cart is empty.</h2>
            <p>Looks like you haven't added anything yet!</p>
            <a href="shop.php" class="btn-primary">⬅ Keep Shopping</a>
        </div>

    <?php else: ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Total</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($productRows as $item): ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?= htmlspecialchars($item['image']) ?>" class="cart-img">
                                <?= htmlspecialchars($item['name']) ?>
                            </div>
                        </td>

                        <td style="text-align:right;">
                            $<?= number_format($item['price'], 2) ?>
                        </td>

                        <td style="text-align:center;"><?= $item['qty'] ?></td>

                        <td style="text-align:right;">
                            $<?= number_format($item['line_total'], 2) ?>
                        </td>

                        <td style="text-align:right;">
                            <form method="post" action="cart_remove.php">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= $item['productId'] ?>">
                                <button type="submit" class="link-button">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr class="subtotal-row">
                    <th colspan="3">Subtotal:</th>
                    <th class="subtotal-val">$<?= number_format($total, 2) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="cart-actions">
            <a href="shop.php" class="btn-secondary">⬅ Keep Shopping</a>
            <a href="checkout.php" class="btn-primary">Proceed to Checkout ➜</a>
        </div>

    <?php endif; ?>
</div>

<?php include "footer.php"; ?>

</body>
</html>
