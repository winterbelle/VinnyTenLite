<?php
session_start();
require_once "config.php";

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR");

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: shop.php");
    exit;
}

$productId = intval($_GET['id']);

// Fetch product using YOUR column names
$stmt = $conn->prepare("SELECT productId, name, description, price, image FROM products WHERE productId = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: shop.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | Vinny Ten Racing</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">

    <style>
        .product-wrapper {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 15px;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .product-image-container {
            flex-shrink: 0; 
        }

        .product-image-container img {
            width: 430px;
            height: auto;
            border-radius: 10px;
        }

        .product-info {
            flex: 1; 
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .product-info h1 {
            font-size: 32px;
            color: #013783;
            margin: 0;
        }

        .product-price {
            font-size: 22px;
            font-weight: bold;
            color: #d50000;
        }

        .product-desc {
            font-size: 16px;
            line-height: 1.6;
        }

        .add-to-cart-form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .add-to-cart-form input[type="number"] {
            width: 70px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-custom {
            padding: 12px 20px;
            background: #013783;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .btn-custom:hover {
            background: #0056b3;
        }

        .btn-back {

            background: #d50000;
        }

        .btn-back:hover {
            background: #a70000;
        }

    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="product-wrapper">

    <div class="product-image-container">
        <img src="<?= htmlspecialchars($product['image']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <div class="product-info">
        
        <h1><?= htmlspecialchars($product['name']) ?></h1>

        <p class="product-price">
            $<?= number_format($product['price'], 2) ?>
        </p>

        <p class="product-desc">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </p>

        <form method="post" action="cart_add.php" class="add-to-cart-form">
            <?php csrf_field(); ?>
            <input type="hidden" name="productId" value="<?= $product['productId'] ?>">

            <label>
                Qty:
                <input type="number" name="qty" value="1" min="1">
            </label>

            <button type="submit" class="btn-custom">ADD TO CART</button>
        </form>

        <a href="shop.php" class="btn-custom btn-back">← BACK TO SHOP</a>

    </div>
</div>


<?php include "footer.php"; ?>

</body>
</html>
