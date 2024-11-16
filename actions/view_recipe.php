<?php
require '../db/db_connection.php';

// Ensure the response is JSON
header('Content-Type: application/json');

// Check if 'id' is passed in the URL
if (isset($_GET['id'])) {
    $foodId = intval($_GET['id']); // Sanitize input

    // Prepare the query to fetch food details, including associated ingredients and recipe data
    $query = "
        SELECT 
            f.food_id, f.name AS food_name, f.origin, f.type, f.is_healthy, f.instructions, f.description, 
            f.preparation_time, f.cooking_time, f.serving_size, f.calories_per_serving, f.image_url,
            r.recipe_id, r.quantity, r.unit, r.optional,
            i.ingredient_id, i.name AS ingredient_name, i.nutritional_value
        FROM foods f
        LEFT JOIN recipes r ON r.food_id = f.food_id
        LEFT JOIN ingredients i ON i.ingredient_id = r.ingredient_id
        WHERE f.food_id = ?
    ";

    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameter
        $stmt->bind_param("i", $foodId);  // 'i' indicates the parameter is an integer

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch the data
        if ($result->num_rows > 0) {
            $recipe = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'recipe' => $recipe]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
    }
}
?>
