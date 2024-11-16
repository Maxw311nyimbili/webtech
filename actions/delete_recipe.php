<?php
require '../db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $foodId = $data['id'];

    try {
        // Begin a transaction to ensure all deletions are handled safely
        $pdo->beginTransaction();

        // First, delete from the recipes table (if any associated recipes exist)
        $stmt = $pdo->prepare("DELETE FROM recipes WHERE food_id = :food_id");
        $stmt->bindParam(':food_id', $foodId);
        $stmt->execute();

        // Next, delete from the foods table
        $stmt = $pdo->prepare("DELETE FROM foods WHERE food_id = :food_id");
        $stmt->bindParam(':food_id', $foodId);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Recipe deleted successfully']);
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $pdo->rollBack();

        echo json_encode(['success' => false, 'message' => 'Failed to delete recipe: ' . $e->getMessage()]);
    }
}
?>

