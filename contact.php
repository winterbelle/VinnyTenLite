<?php
session_start();

$success = isset($_GET["success"]);
$error = isset($_GET["error"]);

$isLoggedIn = isset($_SESSION["user"]);
$userFirst = $isLoggedIn ? $_SESSION["user"]["first"] : null;
$userRole  = $isLoggedIn ? $_SESSION["user"]["role"] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | Vinny Ten Racing</title>
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="dropdown.css">

    <style>
        .contact-wrapper {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
        }
        .contact-wrapper h2 {
            color: #013783;
            font-family: 'Racing Sans One', sans-serif;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .messages .success {
            background: #ddffdd;
            color: #006600;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .messages .error {
            background: #ffdddd;
            color: #990000;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .contact-form input,
        .contact-form textarea {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .contact-form button {
            background-color: #013783;
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .contact-form button:hover {
            background-color: #0056b3;
        }

        .top-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            z-index: 9999;
            animation: fadeOut 2.5s ease forwards;
        }

        .success {
            background-color: #d4f8d4;
            color: #006600;
        }

        .error {
            background-color: #ffdddd;
            color: #990000;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            60% { opacity: 1; }
            100% { opacity: 0; }
        }

    </style>
</head>

<body>
<?php if ($success): ?>
    <div class="top-alert success">Your message has been sent successfully!</div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="top-alert error">Something went wrong. Please try again.</div>
<?php endif; ?>

<?php include "header.php"; ?>


<main class="main-content">
    <div class="contact-wrapper">
        <h2>Contact & Feedback</h2>

        <form class="contact-form" action="process_contact.php" method="post">
            <label>Name</label>
            <input type="text" name="name" required maxlength="100">

            <label>Email</label>
            <input type="email" name="email" required maxlength="150">

            <label>Message</label>
            <textarea name="message" required maxlength="500"></textarea>

            <button type="submit">Send Feedback</button>
        </form>
    </div>
</main>

<?php include "footer.php"; ?>


</body>
</html>
