<?php
// process_contact.php

// 1. Get form values
$name    = trim($_POST["name"] ?? "");
$email   = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

// 2. Validate
$errors = [];

if ($name === "") {
    $errors[] = "Name is required.";
}

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email.";
}

if ($message === "") {
    $errors[] = "Message is required.";
} elseif (strlen($message) > 500) {
    $errors[] = "Message must be 500 characters or less.";
}

// If validation fails, send back to form
if (!empty($errors)) {
    header("Location: contact.html?error=1");
    exit;
}

// 3. Save to database
$host = "localhost";      // change if needed
$user = "root";           // your MySQL username
$pass = "";               // your MySQL password
$db   = "vinnyten_db";    // your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // connection failed – send generic error
    header("Location: contact.html?error=1");
    exit;
}

// Use prepared statement for safety
$stmt = $conn->prepare(
    "INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)"
);

if ($stmt === false) {
    $conn->close();
    header("Location: contact.html?error=1");
    exit;
}

$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();

$stmt->close();
$conn->close();

// 4. Redirect to form with success flag
header("Location: contact.html?success=1");
exit;
