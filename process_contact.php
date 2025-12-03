<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Get form values
$name    = trim($_POST["name"] ?? "");
$email   = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

// 2. Validate
$errors = [];

if ($name === "")       $errors[] = "Name is required.";
if ($email === "")      $errors[] = "Email is required.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Invalid email.";

if ($message === "")    $errors[] = "Message is required.";
elseif (strlen($message) > 500)
    $errors[] = "Message must be 500 characters or less.";

// If validation fails, send back to form
if (!empty($errors)) {
    header("Location: contact.php?error=1");
    exit;
}

// 3. Save to database
$conn = new mysqli("localhost", "root", "", "VTR");

if ($conn->connect_error) {
    header("Location: contact.php?error=1");
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();

$stmt->close();
$conn->close();

// 4. Redirect to form with success flag
header("Location: contact.php?success=1");
exit;
