<?php
session_start();

// If user is not logged in, send them to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Only allow form POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: services.php");
    exit;
}

// Get data from the form
$userId  = (int) $_SESSION['user']['id'];
$name    = trim($_POST['name'] ?? '');
$rating  = (int) ($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Basic checks
if ($name === '' || $rating < 1 || $rating > 5 || $message === '') {
    header("Location: services.php");
    exit;
}

// Limit to about 150 words (extra safety on server)
$wordCount = str_word_count($message);
if ($wordCount > 150) {
    $words   = preg_split('/\s+/', $message);
    $message = implode(' ', array_slice($words, 0, 150));
}

// Connect to database
$pdo = new PDO(
    "mysql:host=localhost;dbname=vinnyten_db;charset=utf8mb4",
    "root",
    ""
);

// Save the review
$sql = "INSERT INTO service_reviews (user_id, name, rating, message, created_at)
        VALUES (:user_id, :name, :rating, :message, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':user_id' => $userId,
    ':name'    => $name,
    ':rating'  => $rating,
    ':message' => $message
]);

// Go back to the reviews page
header("Location: services.php");
exit;

