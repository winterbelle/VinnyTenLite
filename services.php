<?php
session_start();

// Check login
$isLoggedIn = isset($_SESSION['user']);
$userId     = $isLoggedIn ? $_SESSION['user']['id'] : null;
$userName   = $isLoggedIn ? $_SESSION['user']['first'] : null;

// CONNECT TO DATABASE — MySQLi
$conn = new mysqli("localhost", "root", "", "VTR");

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Fetch reviews
$sql = "SELECT name, rating, message, created_at 
        FROM service_reviews 
        ORDER BY created_at DESC";

$result = $conn->query($sql);
$reviews = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance Services | Vinny Ten Racing</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">

    <style>
        .main-content {
            padding: 32px 16px 40px;
            display: flex;
            justify-content: center;
        }

        .services-wrapper {
            background-color: #f5f5f5;
            border-radius: 12px;
            padding: 24px 28px;
            width: 100%;
            max-width: 1100px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .services-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .services-header h1 {
            margin: 0;
            color: #013783;
            font-family: 'Racing Sans One';
            font-size: 30px;
        }

        .review-form-wrapper {
            background: white;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 520px;
        }

        .review-card {
            background: white;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            color: #555;
        }

        .stars {
            color: #f5a623;
            font-size: 18px;
        }
    </style>
</head>

<body>

<?php include "header.php"; ?>

<main class="main-content">
    <div class="services-wrapper">

        
        <div class="services-header">
            <h1>Performance Services Reviews</h1>
            <span>Real feedback from Vinny Ten Racing customers</span>
        </div>

        <!-- REVIEW FORM -->
        <div class="review-form-wrapper">
            <h2>Leave a Review</h2>

            <form class="review-form" action="save_review.php" method="post">

                <?php if ($isLoggedIn): ?>
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name"
                        value="<?= htmlspecialchars($userName) ?>" maxlength="100" readonly>

                <?php else: ?>
                    <label for="name">Name (optional — leave blank to post as Anonymous)</label>
                    <input id="name" type="text" name="name" maxlength="100" placeholder="Your name or leave empty">
                <?php endif; ?>

                <label for="rating">Rating (1–5 stars)</label>
                <select id="rating" name="rating" required>
                    <option value="">Select rating</option>
                    <option value="5">★★★★★ (5)</option>
                    <option value="4">★★★★☆ (4)</option>
                    <option value="3">★★★☆☆ (3)</option>
                    <option value="2">★★☆☆☆ (2)</option>
                    <option value="1">★☆☆☆☆ (1)</option>
                </select>

                <label for="message">Your Review</label>
                <textarea id="message" name="message" maxlength="900" required></textarea>
                <div class="char-note">Up to 150 words (900 characters max).</div>

                <button type="submit">Post Review</button>
            </form>
        </div>

        <!-- LIST OF REVIEWS -->
        <h2>What Our Customers Say</h2>

        <?php if (empty($reviews)): ?>
            <p>No reviews yet.</p>

        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
                <div class="review-card">

                    <div class="review-meta">
                        <strong><?= htmlspecialchars($rev["name"]) ?></strong>
                        <span class="stars">
                            <?= str_repeat("★", $rev["rating"]) . str_repeat("☆", 5 - $rev["rating"]) ?>
                        </span>
                    </div>

                    <div class="review-meta">
                        <span><?= date("M j, Y", strtotime($rev["created_at"])) ?></span>
                    </div>

                    <p><?= nl2br(htmlspecialchars($rev["message"])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
