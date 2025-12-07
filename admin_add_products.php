<?php
session_start();
error_reporting(E_ALL);

// Only admin can access
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: home.php");
    exit();
}

$error = "";
$success = "";

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR");

// Handle Form Submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    $price       = $_POST['price'];

    // ---- IMAGE UPLOAD ----
    if (!empty($_FILES["image"]["name"])) {

        $targetDir = "assets/car-parts/";  // relative path
        $fileName  = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed  = ["jpg", "jpeg", "png", "gif"];

        if (in_array($fileType, $allowed)) {

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {

                // Insert product
                $stmt = $conn->prepare(
                    "INSERT INTO Product (name, description, category_id, price, image_path)
                     VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param("ssids", $name, $description, $category_id, $price, $targetFile);

                if ($stmt->execute()) {
                    $success = "Product added successfully!";
                } else {
                    $error = "Database error while inserting product.";
                }

            } else {
                $error = "Error uploading image.";
            }

        } else {
            $error = "Invalid image format. Only JPG, JPEG, PNG, GIF allowed.";
        }

    } else {
        $error = "Image upload required.";
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
            padding:60px;
            background-color: #f5f5f5;
            border-radius: 12px;
        }

        .admin-form-wrapper h2 {
            color: #013783;
            font-family: 'Racing Sans One', sans-serif;
            font-size: 28px;
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
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .admin-form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .admin-submit-btn {
            background-color: #013783;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .admin-submit-btn:hover {
            background-color: #0056b3;
        }

        /* Success / Error Messages */
        .admin-success {
            background: #ddffdd;
            color: #006600;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .admin-error {
            background: #ffdddd;
            color: #990000;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        /* Back link */
        .admin-back {
            display: inline-block;
            margin-bottom: 12px;
            color: #013783;
            font-weight: bold;
            text-decoration: none;
        }

        .admin-back:hover {
            text-decoration: underline;
        }
    </style>
</head>


<body>
<?php include "header.php"; ?>

<div class="admin-form-wrapper">

    <a href="admin_dashboard.php" class="admin-back">← Back to Dashboard</a>

    <h2>Add New Product</h2>

    <?php if ($success): ?>
        <div class="admin-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="admin-error"><?= $error ?></div>
    <?php endif; ?>

    <form class="admin-form" action="admin_add_products.php" method="POST" enctype="multipart/form-data">

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
            <select name="category" required>
                <option value="">-- Select Category --</option>

                <?php
                $categories = $conn->query("SELECT category_id, name FROM Category");
                while ($cat = $categories->fetch_assoc()) {
                    echo "<option value='{$cat['category_id']}'>{$cat['name']}</option>";
                }
                ?>
            </select>
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
