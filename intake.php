<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cold Air Intake | Vinny Ten Racing</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">

    <style>
        .product-page {
            max-width: 900px;
            margin: 30px auto 50px;
            background: #f5f5f5;
            border-radius: 12px;
            padding: 24px 28px 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            display: grid;
            grid-template-columns: 1.2fr 1.8fr;
            gap: 24px;
        }
        .product-page img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }
        .product-info h1 {
            margin-top: 0;
            font-size: 28px;
            color: #013783;
        }
        .product-price {
            font-size: 22px;
            color: #d50000;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .product-desc {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .product-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-primary,
        .btn-secondary {
            border: none;
            padding: 9px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .btn-primary {
            background: linear-gradient(90deg, #013783, #d50000);
            color: #fff;
        }
        .btn-secondary {
            background: #ffffff;
            color: #013783;
            border: 2px solid #013783;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <section class="product-page">
        <div>
            <img src="assets/intake.jpg" alt="Cold Air Intake">
        </div>
        <div class="product-info">
            <h1>Cold Air Intake</h1>
            <p class="product-price">$299.99</p>

            <p class="product-desc">
                Slap this intake on and the turbo finally gets to breathe.
                You’ll hear that whoosh every time you get on it, like the car’s
                low‑key hyping you up. Pull up to the light, give it a little rev,
                and it’s clear this ain’t no stock grocery‑getter.
            </p>

            <div class="product-actions">
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="2">
                    <button type="submit" class="btn-primary">Add to Cart</button>
                </form>

                <a href="shop.php" class="btn-secondary">Back to Shop</a>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
