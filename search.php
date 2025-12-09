<?php



$conn = new mysqli("localhost", "root", "", "VTR");

$q = trim($_GET['q'] ?? '');
$products = [];
$services = [];

if ($q !== '') {
    $like = '%' . $q . '%';

    // Products
    $stmt = mysqli_prepare(
        $conn,
        "SELECT productId AS id, name, description, price, image
         FROM products
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY name
         LIMIT 50"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        mysqli_stmt_close($stmt);
    }

    // Services
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, description, price, duration
         FROM services
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY name
         LIMIT 50"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">
    <title>Search Results | Vinny Ten Racing</title>
    <style>
        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        
        .search-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
            border: 1px solid #ddd;
            transition: 0.2s ease-in-out;
            text-decoration: none;
            color: inherit;
        }

        .search-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.2);
        }

        .search-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .search-card-body {
            padding: 14px;
        }

        .search-card-body h3 {
            font-size: 18px;
            font-weight: bold;
            color: #013783;
            margin-bottom: 6px;
        }

        .search-card-body .price {
            font-weight: bold;
            color: #d50000;
            margin-bottom: 8px;
        }

        .search-card-body .duration {
            font-size: 14px;
            color: #555;
            font-style: italic;
            margin-bottom: 8px;
        }

        .search-card-body .desc {
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>

    <div class="main-content">

        <h1>Search Results</h1>

        <form action="search.php" method="get" style="margin-bottom:20px;">
            <input type="text" name="q"
                value="<?php echo htmlspecialchars($q); ?>"
                placeholder="Search products &amp; services..."
                style="padding:6px; width:260px; border-radius:6px; border:1px solid #ccc;">
            <button type="submit" class="btn-primary">Search</button>
        </form>

        <?php if ($q === ''): ?>
            <p>Type something in the search box to find products or services.</p>

        <?php else: ?>

            <!-- PRODUCTS SECTION -->
            <h2>Products</h2>

            <?php if (empty($products)): ?>
                <p>No products found for "<?php echo htmlspecialchars($q); ?>".</p>
            <?php else: ?>
                <div class="search-grid">
                    <?php foreach ($products as $p): ?>
                        <a class="search-card" href="product.php?id=<?= $p['id'] ?>">
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">

                            <div class="search-card-body">
                                <h3><?= htmlspecialchars($p['name']) ?></h3>

                                <div class="price">
                                    $<?= number_format((float)$p['price'], 2) ?>
                                </div>

                                <div class="desc">
                                    <?= htmlspecialchars(substr($p['description'], 0, 110)) ?>…
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <!-- SERVICES SECTION -->
            <h2 style="margin-top:40px;">Services</h2>

            <?php if (empty($services)): ?>
                <p>No services found for "<?php echo htmlspecialchars($q); ?>".</p>
            <?php else: ?>
                <div class="search-grid">
                    <?php foreach ($services as $s): ?>
                        <div class="search-card">
                            <div class="search-card-body">
                                <h3><?= htmlspecialchars($s['name']) ?></h3>

                                <div class="price">
                                    $<?= number_format((float)$s['price'], 2) ?>
                                </div>

                                <div class="duration">Duration: <?= htmlspecialchars($s['duration']) ?></div>

                                <div class="desc">
                                    <?= htmlspecialchars(substr($s['description'], 0, 110)) ?>…
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>
