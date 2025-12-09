<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += $qty;
    }
}

$isLoggedIn = isset($_SESSION["user"]);
$userFirst = $isLoggedIn ? $_SESSION["user"]["first"] : null;
$userRole  = $isLoggedIn ? $_SESSION["user"]["role"] : null;
?>
    
<html>
    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <img src="./assets/VTR-Logo-transparent.png" alt="VTR Logo" width="100" height="100">
            <h1>Vinny Ten Racing</h1>
        </div>

        <div class="shop-info">
            <h2>631-414-7590</h2>
            <p>1081 ROUTE 109<br>LINDENHURST, NY 11757</p>
        </div>
    </div>

    <!-- UTILITY BAR -->
    <div class="utility-bar">
        <div class="search">
            <input type="text" placeholder="Search...">
            <button type="submit">🔎</button>
        </div>

        <div class="personal-features">
            
            <?php if (!$isLoggedIn): ?>
                <a href="login.php">🔒 Login</a>
                <a href="signup.php">📝 Sign Up</a>

            <?php else: ?>
                <div class="account-dropdown">
                    <button class="account-btn">
                        👤 <?= htmlspecialchars($userFirst) ?> ▼
                    </button>

                    <div class="dropdown-menu">
                        <?php if ($userRole === "admin"): ?>
                            <a href="admin_dashboard.php">🛠 Admin Dashboard</a>
                        <?php else: ?>
                            <a href="orders.php">📦 View Orders</a>
                            <a href="edit-account.php">⚙️ Edit Profile</a>
                        <?php endif; ?>

                        <a href="logout.php">🚪 Sign Out</a>
                    </div>
                </div>
            <?php endif; ?>

            <a href="wishlist.php">❤️ Wishlist</a>
            <li style="list-style:none;">
                <a href="cart.php" class="cart-link">
                    🛒 Cart 
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
            </li>


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
</html>