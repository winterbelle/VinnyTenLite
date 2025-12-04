<?php
session_start();

// check login
$isLoggedIn = isset($_SESSION['user']);
$userId     = $isLoggedIn ? $_SESSION['user']['id']    : null;
$userName   = $isLoggedIn ? $_SESSION['user']['first'] : null;

// connect to DB (vinnyten_db)
$pdo = new PDO(
    "mysql:host=localhost;dbname=vinnyten_db;charset=utf8mb4",
    "root",
    ""
);

// get all reviews newest first
$stmt = $pdo->query("SELECT name, rating, message, created_at 
                     FROM service_reviews 
                     ORDER BY created_at DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            padding: 24px 28px 30px;
            max-width: 1100px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .services-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .services-header h1 {
            margin: 0;
            color: #013783;
            font-family: 'Racing Sans One', sans-serif;
            font-size: 30px;
            text-transform: uppercase;
        }

        .services-header span {
            font-size: 14px;
            color: #555;
        }

        .review-form-wrapper {
            margin: 20px 0 28px;
            padding: 18px 20px;
            border-radius: 10px;
            background-color: #ffffff;
        }

        .review-form-wrapper h2 {
            margin: 0 0 12px;
            color: #013783;
            font-size: 20px;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 520px;
        }

        .review-form label {
            font-weight: 600;
            color: #013783;
        }

        .review-form input,
        .review-form textarea,
        .review-form select {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        .review-form textarea {
            min-height: 140px;
            resize: vertical;
        }

        .review-form .char-note {
            font-size: 12px;
            color: #666;
        }

        .review-form button {
            margin-top: 6px;
            align-self: flex-start;
            background-color: #013783;
            color: #fff;
            border: none;
            padding: 9px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .review-form button:hover {
            background-color: #0056b3;
        }

        .login-hint {
            font-size: 14px;
            color: #444;
        }
        .login-hint a { color: #013783; }

        .reviews-list h2 {
            margin: 0 0 10px;
            color: #013783;
            font-size: 20px;
        }

        .review-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }

        .stars {
            color: #f5a623;
            font-size: 16px;
        }

        .review-message {
            font-size: 14px;
            color: #222;
            white-space: pre-wrap;
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

        <!-- Review form / login prompt -->
        <div class="review-form-wrapper">
            <?php if ($isLoggedIn): ?>
                <h2>Leave a Review</h2>
                <form class="review-form" action="save_review.php" method="post">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

                    <label for="name">Name</label>
                    <input id="name" type="text" name="name"
                           value="<?= htmlspecialchars($userName) ?>" maxlength="100" required>

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
                    <div class="char-note">Up to about 150 words (900 characters max).</div>

                    <button type="submit">Post Review</button>
                </form>
            <?php else: ?>
                <h2>Want to leave a review?</h2>
                <p class="login-hint">
                    You can read all reviews below. To post your own, please
                    <a href="login.php">log in</a> or <a href="signup.php">create an account</a>.
                </p>
            <?php endif; ?>
        </div>

        <!-- Existing reviews -->
        <div class="reviews-list">
            <h2>What Our Customers Say</h2>

            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to share your experience!</p>
            <?php else: ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="review-meta">
                            <strong><?= htmlspecialchars($rev['name']) ?></strong>
                            <span class="stars">
                                <?php
                                $r = (int)$rev['rating'];
                                echo str_repeat('★', $r) . str_repeat('☆', 5 - $r);
                                ?>
                            </span>
                        </div>
                        <div class="review-meta">
                            <span><?= date('M j, Y', strtotime($rev['created_at'])) ?></span>
                        </div>
                        <p class="review-message"><?= nl2br(htmlspecialchars($rev['message'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include "footer.php"; ?>
</body>
</html>
