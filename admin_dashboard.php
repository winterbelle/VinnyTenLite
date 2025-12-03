<?php
session_start();

// make sure only admin enters
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: home.php");
    exit();
}

$isLoggedIn = true;
$userName = $_SESSION['user']['first'];

// logout alert (just like home)
$logoutMsg = "";
if (isset($_SESSION['logout_message'])) {
    $logoutMsg = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="home.css" />
    <link rel="stylesheet" href="global.css" />
    <link rel="stylesheet" href="dropdown.css" />
    <title>Admin Dashboard</title>

    <style>
        /* logout alert */
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

        /* ADMIN PANEL */
        .admin-dashboard {
            width: 80%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #013783;
            box-shadow: 0 0 20px rgba(1,55,131,0.2);
        }

        .admin-dashboard h2 {
            text-align: center;
            font-size: 30px;
            margin-bottom: 25px;
            text-transform: uppercase;
            color: #013783;
            letter-spacing: 1px;
        }

        .admin-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .admin-buttons a {
            display: block;
            padding: 20px;
            text-align: center;
            background: #013783;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 10px;
            font-size: 20px;
            transition: 0.2s;
        }

        .admin-buttons a:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <?php if (!empty($logoutMsg)): ?>
        <div id="logout-alert"><?= $logoutMsg ?></div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <img src="./assets/VTR-Logo-transparent.png" alt="VTR Logo" width="100" height="100" />
            <h1>Vinny Ten Racing</h1>
        </div>

        <div class="shop-info">
            <h2>631-414-7590</h2>
            <p>1081 ROUTE 109 LINDENHURST, NY 11757</p>
        </div>
    </div>

    <!-- UTILITY BAR -->
    <div class="utility-bar">
        <div class="search">
            <input type="text" placeholder="Search..." />
            <button type="submit">🔎</button>
        </div>

        <div class="personal-features">
            <div class="account-dropdown">
                <button class="account-btn">
                    👤 <?= htmlspecialchars($userName) ?> ▼
                </button>
                <div class="dropdown-menu">
                    <a href="admin_add_products.php">➕ Add Product</a>
                    <a href="admin_dashboard.php">🛠 Dashboard</a>
                    <a href="logout.php">🚪 Sign Out</a>
                </div>
            </div>

            <a href="wishlist.php">❤️ Wishlist</a>
            <a href="cart.php">🛒 Cart</a>
        </div>
    </div>

    <!-- NAV BAR -->
    <div class="nav-bar">
        <a href="home.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="services.php">Performance Services</a>
        <a href="gallery.php">Gallery</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>

    <!-- 🔥 ADMIN CONTENT REPLACES SLIDESHOW -->
    <div class="admin-dashboard">
        <h2>Admin Dashboard</h2>

        <div class="admin-buttons">
            <a href="admin_add_products.php">➕ Add New Product</a>
            <a href="admin_manage_products.php">📦 Manage Products</a>
            <a href="admin_orders.php">🧾 View Orders</a>
            <a href="admin_users.php">👥 Manage Users</a>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2013 Vinny Ten Racing. All rights reserved.</p>
        <div class="footer-links">
            <a href="privacy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
            <a href="contact.php">Contact Us</a>
        </div>
    </footer>

</body>
</html>
