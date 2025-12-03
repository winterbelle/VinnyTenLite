<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // database
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "VTR";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT user_id FROM User WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "An account with this email already exists.";

    } else {
        // Insert into new User table
        $stmt = $conn->prepare(
            "INSERT INTO User (first_name, last_name, email, password_hash, phone_number, role)
             VALUES (?, ?, ?, ?, ?, 'customer')"
        );

        $stmt->bind_param("sssss", $first, $last, $email, $password_hash, $phone);

        if ($stmt->execute()) {

            $success = "Account created successfully! Redirecting to login...";

            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'login.php';
                    }, 2000);
                  </script>";

        } else {
            $error = "Error creating account.";
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: lightgray;
            background-image: linear-gradient(135deg, rgba(1,55,131,0.15), rgba(220,0,0,0.15));
            height: 100vh;

            display: flex;
            flex-direction: column; 
            justify-content: center;
            align-items: center;

            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #013783;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            transform: scale(1.06);
            color: #d50000;
        }

        .register-container {
            background: white;
            padding: 30px;
            width: 380px;
            border-radius: 12px;
            border: 2px solid #013783;
            box-shadow: 0 0 25px rgba(1,55,131,0.6),
                        0 0 10px rgba(220,0,0,0.4);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
            color: #d50000;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .reg-form {
            display: flex;
            flex-direction: column;
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
        }

        .field label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #013783;
            background-color: white;
            color: black;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #d50000;
            box-shadow: 0 0 10px rgba(213,0,0,0.4);
        }

        
        button {
            padding: 12px;
            background: linear-gradient(90deg, #013783, #d50000);
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            letter-spacing: 1px;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover {
            box-shadow: 0 0 15px rgba(213,0,0,0.7);
            transform: scale(1.03);
        }

        
        .top-alert {
            width: 380px;
            text-align: center;
            padding: 12px;
            margin-bottom: 15px;
            font-weight: bold;
            border-radius: 6px;
            font-size: 16px;
        }

        .error { color: rgba(220,0,0,0.85); }
        .success { color: rgba(0,140,0,0.85); }
    </style>
</head>
<body>

    <a href="home.php" class="back-btn">← Back</a>

    <?php if (!empty($error)): ?>
        <div class="top-alert error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="top-alert success"><?= $success ?></div>
    <?php endif; ?>


    <div class="register-container">
        <h2>User Registration</h2>

       <form action="signup.php" method="POST" class="reg-form">

            <div class="field">
                <label>First Name:</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="field">
                <label>Last Name:</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="field">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="field">
                <label>Phone Number:</label>
                <input type="text" name="phone" placeholder="optional">
            </div>

            <div class="field">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Register</button>

        </form>

    </div>

</body>
</html>
