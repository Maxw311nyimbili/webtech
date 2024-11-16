<?php
require '../db/db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = intval($_POST['role']); // Get the role as an integer

    // Initialize an array for error messages
    $errors = [];

    // Server-side Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if ($role !== 1 && $role !== 2) {
        $errors[] = "Invalid role selected.";
    }

    // Check for existing email
    $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $query->execute([$email]);
    if ($query->fetch()) {
        $errors[] = "Email already in use.";
    }

    // If there are errors, display them and stop further execution
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit;
    }

    // Hash password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Set timestamps
    $createdAt = $updatedAt = date("Y-m-d H:i:s");

    try {
        echo "inserting!!!";
        // Insert user data into database
        $query = $pdo->prepare("INSERT INTO users (fname, lname, email, password, role, created_at, updated_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$firstName, $lastName, $email, $hashedPassword, $role, $createdAt, $updatedAt]);

        // Redirect to dashboard.php after successful registration
        header("Location: login.php");
        exit;  // Make sure the script stops after the redirect

    } catch (PDOException $e) {
        // Log the error message in production (do not show it to users)
        echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Link | Sign-up</title>
    <link rel="stylesheet" href="../assets/css/sign-up.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <script src="../assets/js/sign-up.js" defer></script> -->
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div><a href="../view/index.html"><img class="logo" src="../assets/images/logo.png" alt="website logo"></a></div>
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="../index.php" class="menu-item">Home</a></li>
                    <li><a href="./login.php" class="menu-item">Login</a></li>
                    <li><a href="./sign-in.php" class="menu-item" style="color: #E6003D;">Sign-in</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Sign-up Form -->
    <div class="container">
        <div class="banner-login"></div>
        <div class="right-content">
            <div class="header-wrapper">
                <div class="heading" style="color: #122331;"><h1>Create an Account</h1></div>
            </div>
            
            <div class="inner-container">

                <form id="registerForm" method="POST" action="sign-in.php">
                    <div><input class="input_area" type="text" id="firstName" name="firstName" placeholder="First Name" required></div>
                    <div id="firstNameError" class="error-message"></div>
                    
                    <div><input class="input_area" type="text" id="lastName" name="lastName" placeholder="Last Name" required></div>
                    <div id="lastNameError" class="error-message"></div>
                    
                    <div><input  class="input_area" type="email" id="email" name="email" placeholder="Email" required></div>
                    <div id="emailError" class="error-message"></div>

                    <div><input class="input_area" type="password" id="password" name="password" placeholder="Password" required></div>
                    <div id="passwordError" class="error-message"></div>

                    <div><input class="input_area" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required></div>
                    <div id="confirmPasswordError" class="error-message"></div>

                    <!-- Role Selection -->
                    <div>
                        <label for="role">Select Role:</label>
                        <select name="role" id="role" required>
                            <!-- Super Admin -->
                            <option value="1">Admin</option>

                            <!-- Regular Admin -->
                            <option value="2">Regular user</option>
                        </select>
                    </div>

                    <div><button class = "submit_btn" type="submit">Submit</button></div>
                    <div class="create"><p>Already have an account?</p> <a href="./login.php">Login</a></div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
