<?php
session_start();

// Check if logged in
$isLoggedIn = isset($_SESSION['user']);
$userFirstName = $isLoggedIn ? $_SESSION['user']['first'] : null;
$userRole = $isLoggedIn ? $_SESSION['user']['role'] : null;

// Logout message support
$logoutMsg = "";
if (isset($_SESSION['logout_message'])) {
    $logoutMsg = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <title>Shop | Vinny Ten Racing</title>

    <style>
        /* SHOP GRID */
        .shop-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .shop-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #013783;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: white;
            border: 2px solid #013783;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            transition: 0.25s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        .product-card img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            border-radius: 6px;
        }

        .product-card h3 {
            margin: 10px 0 5px;
            font-size: 18px;
        }

        .product-card p {
            margin: 5px 0;
            font-weight: bold;
            color: #d50000;
        }

        .product-card button {
            margin-top: 10px;
            background: linear-gradient(90deg, #013783, #d50000);
            border: none;
            padding: 10px 14px;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .product-card button:hover {
            transform: scale(1.05);
        }

        /* ALERT STYLE */
        #logout-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #013783;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            z-index: 9999;
            animation: fadeOut 1.8s ease forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; transform: translateX(-50%) translateY(0); }
            70% { opacity: 1; }
            100% { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        }
    </style>
</head>

<body>

<?php if (!empty($logoutMsg)): ?>
    <div id="logout-alert"><?= $logoutMsg ?></div>
<?php endif; ?>

<?php include "header.php"; ?>


<!-- SHOP CONTENT -->
<div class="shop-container">
    <h1 class="shop-title">Shop All Products</h1>

    <div class="product-grid">

        <!-- SAMPLE STATIC PRODUCTS (replace later with DB loop) -->
        <div class="product-card">
            <img src="./assets/exhaust.jpg" alt="Exhaust">
            <h3>Performance Exhaust</h3>
            <p>$499.99</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="./assets/intake.jpg" alt="Air Intake">
            <h3>Cold Air Intake</h3>
            <p>$299.99</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="./assets/coilovers.jpg" alt="Coilovers">
            <h3>Adjustable Coilovers</h3>
            <p>$799.99</p>
            <button>Add to Cart</button>
        </div>

        <div class="product-card">
            <img src="./assets/downpipe.jpg" alt="Downpipe">
            <h3>Downpipe Kit</h3>
            <p>$349.99</p>
            <button>Add to Cart</button>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>


</body>
</html>
