<?php
session_start();

$isLoggedIn = isset($_SESSION['user']);
$userId     = $isLoggedIn ? $_SESSION['user']['id'] : null;
$userName   = $isLoggedIn ? $_SESSION['user']['first'] : null;


$conn = new mysqli("localhost", "root", "", "VTR");
if ($conn->connect_error) { die("DB Connection failed: " . $conn->connect_error); }


$services = [];
$serviceQuery = $conn->query(
    "SELECT id, name, description, price, duration
     FROM services
     ORDER BY name"
);
if ($serviceQuery && $serviceQuery->num_rows > 0) {
    $services = $serviceQuery->fetch_all(MYSQLI_ASSOC);
}


$bookingSuccess = null;
$bookingError   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {

    if (!$isLoggedIn) {
        $bookingError = "You must be logged in to make a booking.";
    } else {
        $service_id   = (int)($_POST['service_id'] ?? 0);
        $booking_date = trim($_POST['booking_date'] ?? '');
        $notes        = trim($_POST['notes'] ?? '');

        if ($service_id <= 0 || empty($booking_date)) {
            $bookingError = "Please choose a service and a valid date/time.";
        } else {
            $timestamp = strtotime($booking_date);
            if ($timestamp === false || $timestamp < time()) {
                $bookingError = "Booking date must be in the future.";
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO bookings (user_id, service_id, booking_date, notes, created_at)
                     VALUES (?, ?, ?, ?, NOW())"
                );
                $stmt->bind_param("iiss", $userId, $service_id, $booking_date, $notes);
                $stmt->execute();
                $stmt->close();

                $bookingSuccess = "Your booking was created successfully!";
            }
        }
    }
}


$reviewsQuery = $conn->query(
    "SELECT name, rating, message, created_at 
     FROM service_reviews 
     ORDER BY created_at DESC"
);
$reviews = $reviewsQuery ? $reviewsQuery->fetch_all(MYSQLI_ASSOC) : [];


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
            width: 100%;
            max-width: 1100px;
            background: #f5f5f5;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .section-title {
            color: #013783;
            font-family: 'Racing Sans One';
            font-size: 28px;
            margin-bottom: 14px;
        }


        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .services-table th {
            background: #013783;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .services-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .services-table tr:last-child td {
            border-bottom: none;
        }

        .booking-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .booking-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 6px;
            margin-bottom: 15px;
        }

        .btn-book {
            background: #013783;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-book:hover {
            background: #0050b3;
        }

        .review-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 14px;
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

        .alert-success {
            padding: 10px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            margin-bottom: 15px;
        }

        .alert-error {
            padding: 10px;
            background: #f8d7da;
            border-left: 4px solid #c82333;
            margin-bottom: 15px;
        }

        .btn-custom {
            padding: 10px 16px;
            background: #013783;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .btn-custom:hover {
            background: #0056b3;
        }

        .review-form-wrapper {
            background: white;
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .review-form-wrapper h2 {
            color: #013783;
            font-size: 24px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #013783;
        }

        .review-form input,
        .review-form select,
        .review-form textarea {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccd2de;
            font-size: 15px;
            background: #f8f9fc;
        }

        .review-form textarea {
            resize: vertical;
        }

        .char-note {
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }

        .btn-submit-review {
            padding: 12px 20px;
            margin-top: 10px;
            background: #013783;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-submit-review:hover {
            background: #0050b3;
        }



    </style>
    <script src="scripts/review-form.js" defer></script>
</head>

<body>

<?php include "header.php"; ?>

<main class="main-content">
    <div class="services-wrapper">

        <h1 class="section-title">Performance Services</h1>

        <?php if ($bookingSuccess): ?>
            <div class="alert-success"><?= htmlspecialchars($bookingSuccess) ?></div>
        <?php endif; ?>

        <?php if ($bookingError): ?>
            <div class="alert-error"><?= htmlspecialchars($bookingError) ?></div>
        <?php endif; ?>

        <h2 class="section-title">Available Services</h2>

        <?php if (empty($services)): ?>
            <p>No services available at this time.</p>
        <?php else: ?>
            <table class="services-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $srv): ?>
                    <tr>
                        <td><?= htmlspecialchars($srv['name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($srv['description'])) ?></td>
                        <td><?= htmlspecialchars($srv['duration']) ?></td>
                        <td>$<?= number_format($srv['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>


        <h2 class="section-title">Book a Service</h2>

        <?php if (!$isLoggedIn): ?>
            <p>You must <a href="login.php">log in</a> to book a service.</p>

        <?php elseif (empty($services)): ?>
            <p>No services available for booking.</p>

        <?php else: ?>
            <div class="booking-box">
                <form method="post">

                    <label>Service</label>
                    <select name="service_id" class="booking-input" required>
                        <option value="">-- Choose a Service --</option>
                        <?php foreach ($services as $srv): ?>
                            <option value="<?= $srv['id'] ?>">
                                <?= htmlspecialchars($srv['name']) ?> — $<?= number_format($srv['price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Preferred Date & Time</label>
                    <input type="datetime-local" name="booking_date" class="booking-input" required>

                    <label>Notes (optional)</label>
                    <textarea name="notes" class="booking-input" rows="4"></textarea>

                    <button class="btn-book">Book Service</button>
                </form>
            </div>
        <?php endif; ?>

        <h2>What Our Customers Say</h2>

        <!-- Leave Review Button -->
        <button id="toggleReviewBtn" class="btn-custom" style="margin-bottom:15px;">
            ✏️ Leave a Review
        </button>

        <!-- Hidden Review Form -->
        <div id="reviewFormContainer" class="review-form-wrapper" style="display:none;">

            <h2 style="margin-bottom:12px;">Leave a Review</h2>

            <form class="review-form" action="save_review.php" method="post">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <?php if ($isLoggedIn): ?>
                            <input id="name" type="text" name="name"
                                value="<?= htmlspecialchars($userName) ?>" readonly>
                        <?php else: ?>
                            <input id="name" type="text" name="name"
                                placeholder="Leave empty for Anonymous">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required>
                            <option value="">Select rating</option>
                            <option value="5">★★★★★ (5)</option>
                            <option value="4">★★★★☆ (4)</option>
                            <option value="3">★★★☆☆ (3)</option>
                            <option value="2">★★☆☆☆ (2)</option>
                            <option value="1">★☆☆☆☆ (1)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Your Review</label>
                    <textarea id="message" name="message" rows="4"
                            placeholder="Write your experience…" required></textarea>
                    <div class="char-note">Max 150 words (900 characters).</div>
                </div>

                <button type="submit" class="btn-submit-review">Submit Review</button>
            </form>

        </div>
        <?php if (empty($reviews)): ?>
            <p>No reviews yet.</p>

        <?php else: ?>
            <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <div class="review-meta">
                        <strong><?= htmlspecialchars($r["name"]) ?></strong>
                        <span class="stars"><?= str_repeat("★", $r["rating"]) . str_repeat("☆", 5 - $r["rating"]) ?></span>
                    </div>

                    <div class="review-meta">
                        <span><?= date("M j, Y", strtotime($r["created_at"])) ?></span>
                    </div>

                    <p><?= nl2br(htmlspecialchars($r["message"])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</main>

<?php include "footer.php"; ?>
</body>
</html>
