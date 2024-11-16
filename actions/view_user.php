<?php
// Include the database connection file
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);  // Sanitize the input

    // Prepare the query to fetch the user details
    $query = "SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users WHERE user_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameter
        $stmt->bind_param("i", $userId);  // 'i' indicates the parameter is an integer

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch the user data
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();  // Fetch the associative array
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

?>

