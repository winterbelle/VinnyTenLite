<?php
session_start();
error_reporting(E_ALL);

// Redirect if not logged in or not admin
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: home.php");
    exit();
}

$error = "";
$success = "";

// Database
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "VTR";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $price = $_POST['price'];

    // Handle image upload
    if (!empty($_FILES["image"]["name"])) {

        $targetDir = "/Applications/XAMPP/htdocs/Belle-Mac272/Vinny-Ten-Lite/assets/car-parts";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowedTypes)) {

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {

                $stmt = $conn->prepare(
                    "INSERT INTO Product (name, description, category_id, price, image_path)
                     VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param("ssids", $name, $description, $category_id, $price, $targetFilePath);

                if ($stmt->execute()) {
                    $success = "Product added successfully!";
                } else {
                    $error = "Database insert error.";
                }

            } else {
                $error = "Error uploading the image.";
            }

        } else {
            $error = "Only JPG, JPEG, PNG, GIF images allowed.";
        }
    } else {
        $error = "Please upload a product image.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product (Admin)</title>
    <style>
        body {
            background-color: #e9edf3;
            font-family: Arial, Helvetica, sans-serif;
            padding: 30px;
        }

        .container {
            width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #013783;
            box-shadow: 0 0 20px rgba(1,55,131,0.4);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #013783;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #013783;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #013783;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(90deg, #013783, #d50000);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            letter-spacing: 1px;
        }

        button:hover {
            transform: scale(1.02);
            box-shadow: 0 0 12px rgba(1,55,131,0.5);
        }

        .msg-success {
            color: green;
            margin-bottom: 15px;
            text-align: center;
        }

        .msg-error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        .back-btn {
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
            font-weight: bold;
            color: #013783;
        }
    </style>
</head>
<body>

    <a href="admin_dashboard.php" class="back-btn">← Back to Admin Dashboard</a>

    <div class="container">
        <h2>Add New Product</h2>

        <?php if ($success): ?>
            <div class="msg-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="msg-error"><?= $error ?></div>
        <?php endif; ?>

        <form action="admin_add_products.php" method="POST" enctype="multipart/form-data">

            <label>Product Name:</label>
            <input type="text" name="name" required>

            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Category:</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>

                <?php
                // Load categories
                $result = $conn->query("SELECT category_id, name FROM Category");

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['category_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <label>Price ($):</label>
            <input type="number" name="price" step="0.01" required>

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="submit">➕ Add Product</button>
        </form>
    </div>

</body>
</html>
