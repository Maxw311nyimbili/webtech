<?php
require '../db/db_connection.php';

header('Content-Type: application/json'); // Ensure the response is JSON

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

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $foodId); // Bind the food ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all results as an associative array
        $recipe = $result->fetch_all(MYSQLI_ASSOC);

        if ($recipe) {
            echo json_encode(['success' => true, 'recipe' => $recipe]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the query']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Food ID is required']);
}

?>
