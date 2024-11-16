<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email
    $password = 'kitchen1234';  // Default password
    $role = intval($_POST['role']); // Convert role to integer

    // Hash the password before storing it (for security)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email is already in use
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }

    // Prepare the SQL query to insert the new user
    $query = $pdo->prepare("INSERT INTO users (fname, lname, email, password, role) VALUES (?, ?, ?, ?, ?)");
    
    // Execute the query with the provided values
    $query->execute([$firstName, $lastName, $email, $hashedPassword, $role]);

    // Check if the insert was successful
    if ($query->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
