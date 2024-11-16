<?php
require '../db/db_connection.php';

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Parse the request body to get the id
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (isset($_DELETE['id'])) {
        $userId = intval($_DELETE['id']); // Sanitize the input

        // Prepare the DELETE query using the correct column name (user_id)
        $query = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $query->bindValue(1, $userId, PDO::PARAM_INT); // Bind the user ID parameter

        // Check if the delete query was successful
        if ($query->execute()) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
    }
} else {
    // Invalid request method, return JSON error
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
