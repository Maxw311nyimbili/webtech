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
        SET food_id = :food_id, ingredient_id = :ingredient_id, quantity = :quantity, unit = :unit, optional = :optional
        WHERE recipe_id = :recipe_id
    ";

    // Prepare the statement
    if ($stmt = $pdo->prepare($query)) {
        // Bind parameters
        $stmt->bindParam(':food_id', $food_id, PDO::PARAM_INT);
        $stmt->bindParam(':ingredient_id', $ingredient_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR); // Assuming quantity is a decimal or string
        $stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
        $stmt->bindParam(':optional', $optional, PDO::PARAM_INT); // Assuming optional is an integer (boolean)
        $stmt->bindParam(':recipe_id', $recipe_id, PDO::PARAM_INT);

        // Execute the statement and handle success/error
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Recipe updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating recipe']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
}
?>

