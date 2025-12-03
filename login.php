<?php
session_start();
error_reporting(E_ALL);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // database
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "VTR";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    $stmt = $conn->prepare(
        "SELECT user_id, first_name, last_name, email, password_hash, role 
         FROM User WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($id, $first, $last, $em, $pass_hash, $role);
        $stmt->fetch();

        if (password_verify($password, $pass_hash)) {

            $_SESSION["user"] = [
                "id" => $id,
                "first" => $first,
                "last" => $last,
                "email" => $em,
                "role" => $role
            ];

            $success = "Login successful! Redirecting...";

            echo "<script>
            setTimeout(function(){
                if ('$role' === 'admin') {
                    window.location.href = 'admin_dashboard.php';
                } else {
                    window.location.href = 'home.php';
                }
            }, 3000);
            </script>";
        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "No account found with that email.";
    }
}
?>


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: lightgray;
            background-image: linear-gradient(135deg, rgba(1,55,131,0.15), rgba(220,0,0,0.15));
            height: 100vh;

            display: flex;
            flex-direction: column;       /* IMPORTANT */
            justify-content: center;
            align-items: center;

            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        login-form {
            display: flex;
            flex-direction: column;
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .field label {
            margin-bottom: 5px;
        }

        .login-container {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 12px;
            border: 2px solid #013783;
            box-shadow: 0 0 25px rgba(1,55,131,0.6),
                        0 0 10px rgba(220,0,0,0.4);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
            color: #d50000;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
        }

        label {
            margin: 8px 0 5px;
            font-weight: bold;
            font-size: 14px;
            color: #111111;
        }

        input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #013783;
            background-color: white;
            color: black;
            outline: none;
            margin-bottom: 15px;
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
            margin-top: 5px;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        button:hover {
            box-shadow: 0 0 15px rgba(213,0,0,0.7);
            transform: scale(1.03);
        }

        .signup-text {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        .signup-text a {
            color: #d50000;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }

        .top-alert {
            
            text-align: center;
            padding: 12px;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 16px;
        }

        .error {
            color: rgba(220, 0, 0, 0.85);
        }

        .success {
            color: rgba(0, 140, 0, 0.85);
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #013783;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .back-btn:hover {
            transform: scale(1.05);
        }

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

    <div class="login-container">
        <h2>User Login</h2>
        <form action="login.php" method="POST" class="login-form">
            <div class="field">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>


        <div class="signup-text">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
</body>
</html>
