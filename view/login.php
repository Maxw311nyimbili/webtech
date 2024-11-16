<?php
require '../db/db_connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side Validation
    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit;
    }

    // Check credentials in the database
    $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Successful login, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['fname'];
        $_SESSION['last_name'] = $user['lname'];
        $_SESSION['role'] = $user['role'];

        // Redirect to the dashboard page
        header("Location: admin/dashboard.php");
        exit;
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Link | Login Page</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <script src="../assets/js/login.js" defer></script> -->
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div><a href="../view/index.html"><img class="logo" src="../assets/images/logo.png" alt="website logo"></a></div>
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="../index.php" class="menu-item">Home</a></li>
                    <li><a href="./login.php" class="menu-item" style="color: #E6003D;">Login</a></li>
                    <li><a href="./sign-in.php" class="menu-item">Sign-in</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Login Form -->
    <div class="container">
        <div class="banner-login"></div>
        <div class="right-content">
            <div class="header-wrapper">
                <div class="heading" style="color: #122331;"><h1>WELCOME BACK!</h1></div>
            </div>
            
            <div class="inner-container">
                <form id="loginForm" method="POST" action="login.php">
                    <div><input id="email" class="input_area" type="email" name="email" placeholder="Enter your email"></div>
                    <div id="emailError" class="error-message"></div>

                    <div><input id="password" class="input_area" type="password" name="password" placeholder="Enter your password" ></div>
                    <div id="passwordError" class="error-message"></div>


                    <div class="forgot-section">

                        <div class="forgot"><a href="#" style="color: #E6003D;">forgot password?</a></div>

                    </div>

                    <div class="btn-wrapper">
                        <div><input class="submit_btn" type="submit" value="Log in"></div>
                        <div class="create"><p>Do not have an account?</p> <a href="./sign-in.php">Sign-up</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
