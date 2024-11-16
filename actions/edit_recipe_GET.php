<?php
require '../db/db_connection.php';

if (isset($_GET['id'])) {
    $foodId = $_GET['id'];

    // Prepare the query to fetch food details, including associated ingredients and recipe data
    $stmt = $pdo->prepare("
        SELECT 
            f.food_id, f.name AS food_name, f.origin, f.type, f.is_healthy, f.instructions, f.description, 
            f.preparation_time, f.cooking_time, f.serving_size, f.calories_per_serving, f.image_url,
            r.recipe_id, r.quantity, r.unit, r.optional,
            i.ingredient_id, i.name AS ingredient_name, i.nutritional_value
        FROM foods f
        LEFT JOIN recipes r ON r.food_id = f.food_id
        LEFT JOIN ingredients i ON i.ingredient_id = r.ingredient_id
        WHERE f.food_id = :food_id
    ");
    $stmt->bindParam(':food_id', $foodId);
    $stmt->execute();

    // Fetch the recipe data, which will include the food details and its ingredients
    $recipe = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($recipe) {
        echo json_encode(['success' => true, 'recipe' => $recipe]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Recipe not found']);
    }
}
?>

