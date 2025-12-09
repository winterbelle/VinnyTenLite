<?php
session_start();
error_reporting(E_ALL);

// Only admin can access
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "VTR");

$error = "";
$success = "";

// Handle the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category    = trim($_POST['category']);
    $price       = trim($_POST['price']);

    if ($name === "" || $description === "" || $category === "" || $price === "") {
        $error = "All fields are required.";
    } elseif (!is_numeric($price)) {
        $error = "Price must be a number.";
    } elseif (!empty($_FILES["image"]["name"])) {

        // Upload directory
        $targetDir = "assets/car-parts/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName  = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($fileType, $allowed)) {
            $error = "Invalid image format. Use JPG, JPEG, PNG, or GIF.";
        } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {

            // Insert into YOUR table
            $stmt = $conn->prepare(
                "INSERT INTO products (name, description, category, price, image)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param("sssds", $name, $description, $category, $price, $targetFile);

            if ($stmt->execute()) {
                $success = "Product added successfully!";
            } else {
                $error = "Database error: " . $stmt->error;
            }

            $stmt->close();

        } else {
            $error = "Failed to upload image.";
        }

    } else {
        $error = "Image upload is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | Admin</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">

    <style>
        .admin-form-wrapper {
            max-width: 800px;
            margin: 30px auto;
            padding: 40px;
            background-color: #f5f5f5;
            border-radius: 12px;
        }

        h2 {
            color: #013783;
            font-family: 'Racing Sans One';
            margin-bottom: 15px;
        }

        .admin-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .admin-form label {
            font-weight: bold;
            color: #013783;
        }

        .admin-form input,
        .admin-form textarea,
        .admin-form select {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .admin-submit-btn {
            background-color: #013783;
            color: white;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .admin-submit-btn:hover {
            background-color: #0056b3;
        }

        .admin-success {
            background: #ddffdd;
            padding: 12px;
            border-radius: 6px;
            color: #007700;
        }

        .admin-error {
            background: #ffdddd;
            padding: 12px;
            border-radius: 6px;
            color: #990000;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<div class="admin-form-wrapper">

    <a href="admin_dashboard.php" style="text-decoration:none;color:#013783;font-weight:bold;">← Back to Dashboard</a>

    <h2>Add New Product</h2>

    <?php if ($success): ?>
        <div class="admin-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="admin-error"><?= $error ?></div>
    <?php endif; ?>

    <form class="admin-form" method="POST" enctype="multipart/form-data">

        <div>
            <label>Product Name</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>

        <div>
            <label>Category</label>
            <input type="text" name="category" required>
        </div>

        <div>
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" required>
        </div>

        <div>
            <label>Product Image</label>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <button type="submit" class="admin-submit-btn">➕ Add Product</button>

    </form>
</div>

<?php include "footer.php"; ?>

</body>
</html>
