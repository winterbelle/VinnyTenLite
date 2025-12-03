<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

$userFirst = $_SESSION['user']['first'];

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR");
$feedbackCount = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VTR</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <style>
        .dashboard-wrapper {
            width: 90%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #013783;
        }

        .dashboard-wrapper h2 {
            font-size: 32px;
            color: #013783;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .dash-card {
            background: #f5f9ff;
            border: 2px solid #013783;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: 0.3s ease;
        }

        .dash-card:hover {
            background: #e8f0ff;
            transform: translateY(-4px);
        }

        .dash-card h3 {
            margin: 0;
            font-size: 22px;
            color: #013783;
        }

        .dash-card p {
            font-size: 16px;
            margin-top: 8px;
        }

        .dash-card a {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 15px;
            background: #013783;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .dash-card a:hover {
            background: #d50000;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="dashboard-wrapper">
    <h2>Welcome Admin!</h2>

    <div class="dashboard-grid">

        <!-- Feedback -->
        <div class="dash-card">
            <h3>📨 Feedback Messages</h3>
            <p>Total Messages: <strong><?= $feedbackCount ?></strong></p>
            <a href="admin_feedback.php">View Messages</a>
        </div>

        <!-- Add Product -->
        <div class="dash-card">
            <h3>➕ Add Product</h3>
            <p>Manage shop inventory</p>
            <a href="admin_add_products.php">Add Product</a>
        </div>

        <!-- Manage Products -->
        <div class="dash-card">
            <h3>🛠 Manage Products</h3>
            <p>Edit / Delete existing inventory</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Orders -->
        <div class="dash-card">
            <h3>📦 Orders</h3>
            <p>Track customer purchases</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Manage Users -->
        <div class="dash-card">
            <h3>👥 Manage Users</h3>
            <p>Promote / ban / edit users</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Slideshow Manager -->
        <div class="dash-card">
            <h3>🖼 Homepage Banners</h3>
            <p>Change slideshow images</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Low stock alerts -->
        <div class="dash-card">
            <h3>⚠️ Low Stock Alerts</h3>
            <p>See products running low</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Category Manager -->
        <div class="dash-card">
            <h3>🏷 Manage Categories</h3>
            <p>Add / rename categories</p>
            <a href="#">Coming Soon</a>
        </div>

        <!-- Shop Settings -->
        <div class="dash-card">
            <h3>⚙️ Shop Settings</h3>
            <p>Site-wide controls</p>
            <a href="#">Coming Soon</a>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>

</body>
</html>
