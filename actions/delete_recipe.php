<?php
require '../db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $foodId = $data['id'];

    // Ensure the response is JSON
    header('Content-Type: application/json');

    // Start a transaction
    $conn->begin_transaction();

    try {
        // First, delete from the recipes table (if any associated recipes exist)
        $stmt = $conn->prepare("DELETE FROM recipes WHERE food_id = ?");
        $stmt->bind_param("i", $foodId);
        $stmt->execute();
        $stmt->close();

        // Next, delete from the foods table
        $stmt = $conn->prepare("DELETE FROM foods WHERE food_id = ?");
        $stmt->bind_param("i", $foodId);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Recipe deleted successfully']);
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollback();

        echo json_encode(['success' => false, 'message' => 'Failed to delete recipe: ' . $e->getMessage()]);
    }

}
?>


