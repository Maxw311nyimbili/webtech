<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);  // Sanitize the input
    $query = $pdo->prepare("SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users WHERE user_id = ?");
    $query->execute([$userId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Check if the user was found
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

?>
