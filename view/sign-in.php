<?php
require '../db/db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

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

    // Check for existing email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email already in use.";
    }
    $stmt->close();

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

    // Assign default role (2 = Regular user)
    $role = 2;

    // Insert user data into database
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, password, role, created_at, updated_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('ssssiss', $firstName, $lastName, $email, $hashedPassword, $role, $createdAt, $updatedAt);

        if ($stmt->execute()) {
            // Redirect to login.php after successful registration
            header("Location: login.php");
            exit;
        } else {
            echo "<p style='color: red;'>An error occurred while inserting data. Please try again later.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Failed to prepare the SQL statement.</p>";
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

                    <!-- Role Selection removed, Default role set to Regular user (2) -->

                    <div><button class="submit_btn" type="submit">Submit</button></div>
                    <div class="create"><p>Already have an account?</p> <a href="./login.php">Login</a></div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
