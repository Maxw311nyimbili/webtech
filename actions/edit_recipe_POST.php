<?php
require '../db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['id'];
    $food_id = $_POST['food_id'];
    $ingredient_id = $_POST['ingredient_id'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $optional = $_POST['optional'];

    // Update recipe details
    $query = "
        UPDATE recipes 
        SET food_id = ?, ingredient_id = ?, quantity = ?, unit = ?, optional = ?
        WHERE recipe_id = ?
    ";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($query)) {
        // Bind parameters
        $stmt->bind_param("iiissi", $food_id, $ingredient_id, $quantity, $unit, $optional, $recipe_id); 

        // Execute the statement and handle success/error
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Recipe updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating recipe']);
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
}
?>
