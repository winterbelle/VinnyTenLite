<?php
session_start();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: services.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user']);
$userId = $isLoggedIn ? (int)$_SESSION['user']['id'] : null;

// If logged in → use their name
if ($isLoggedIn) {
    $name = trim($_SESSION['user']['first']);
} else {
    // If not logged in → optional name
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $name = "Anonymous";
    }
}

$rating  = (int) ($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Validation
if ($rating < 1 || $rating > 5 || $message === '') {
    header("Location: services.php");
    exit;
}

// Word limit safety
$wordCount = str_word_count($message);
if ($wordCount > 150) {
    $words = preg_split('/\s+/', $message);
    $message = implode(' ', array_slice($words, 0, 150));
}

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR"); // use YOUR DB

// Insert review
$stmt = $conn->prepare(
    "INSERT INTO service_reviews (user_id, name, rating, message, created_at)
     VALUES (?, ?, ?, ?, NOW())"
);

$stmt->bind_param("isis", $userId, $name, $rating, $message);
$stmt->execute();

$stmt->close();
$conn->close();

// Redirect back
header("Location: services.php");
exit;
