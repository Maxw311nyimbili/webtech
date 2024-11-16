<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the user ID from the query string
    if (isset($_GET['id'])) {
        $userId = intval($_GET['id']);  // Sanitize the ID

        // Prepare the query to fetch the user data
        $query = $pdo->prepare("SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users WHERE user_id = ?");
        $query->execute([$userId]);

        // Check if user data was found
        if ($query->rowCount() > 0) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

