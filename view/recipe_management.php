<?php
// Database connection using MySQLi
require '../db/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction
        $conn->begin_transaction();

        // Step 1: Sanitize and validate inputs
        $recipeName = trim($_POST['recipe-name']);
        $firstName = trim($_POST['first-name']);
        $lastName = trim($_POST['last-name']);
        $date = $_POST['date'];
        $country = $_POST['countrySelect'];
        $ingredientList = explode("\n", trim($_POST['ingredient-list']));
        $allergies = trim($_POST['allergies']);
        $shelfLife = (int)$_POST['shelf-life'];
        $prepTime = (int)$_POST['prep-time'];
        $cookTime = (int)$_POST['cook-time'];
        $calories = (int)$_POST['calories'];
        $instructions = trim($_POST['instructions']);
        
        // Handle file upload
        $imagePath = null; // Default
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $imageDir = 'uploads/';
            $imageName = basename($_FILES['file']['name']);
            $imagePath = $imageDir . $imageName;

            if (!is_dir($imageDir)) {
                mkdir($imageDir, 0777, true);
            }

            if (!move_uploaded_file($_FILES['file']['tmp_name'], $imagePath)) {
                throw new Exception("Failed to upload the image.");
            }
        }

        // Step 2: Fetch user ID
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE LOWER(fname) = LOWER(?) AND LOWER(lname) = LOWER(?)");
        $stmt->bind_param('ss', $firstName, $lastName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Author not found.");
        }

        $user = $result->fetch_assoc();
        $createdBy = $user['user_id'];

        // Step 3: Insert into `foods`
        $stmt = $conn->prepare("
            INSERT INTO foods (name, origin, preparation_time, cooking_time, calories_per_serving, instructions, image_url, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiiissis', $recipeName, $country, $prepTime, $cookTime, $calories, $instructions, $imagePath, $createdBy, $date);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Failed to insert recipe.");
        }

        $foodId = $conn->insert_id;

        // Step 4: Insert ingredients and link to recipe
        foreach ($ingredientList as $ingredient) {
            preg_match('/(?P<name>[^:]+):\s*(?P<quantity>[0-9.]+)\s*(?P<unit>[a-zA-Z]+)?\s*(?:\(optional\))?/', trim($ingredient), $matches);

            if ($matches) {
                $ingredientName = $matches['name'];
                $quantity = $matches['quantity'];
                $unit = isset($matches['unit']) ? $matches['unit'] : null;
                $optional = strpos($matches[0], 'optional') !== false ? 1 : 0;

                // Insert into `ingredients`
                $stmt = $conn->prepare("
                    INSERT INTO ingredients (name, origin, nutritional_value, allergen_info, shelf_life, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param('ssisis', $ingredientName, $country, $calories, $allergies, $shelfLife, $date);
                $stmt->execute();

                $ingredientId = $mysqli->insert_id;

                // Link ingredient to food via `recipes`
                $stmt = $conn->prepare("
                    INSERT INTO recipes (food_id, ingredient_id, quantity, unit, optional, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param('iissis', $foodId, $ingredientId, $quantity, $unit, $optional, $date);
                $stmt->execute();
            } else {
                error_log("Invalid ingredient format: $ingredient");
            }
        }

        // Commit transaction
        $conn->commit();
        echo "Recipe added successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        echo "Transaction failed: " . $e->getMessage();
    }
}

// Fetch recipes from the database
$stmt = $conn->prepare("
    SELECT foods.food_id, foods.name, foods.origin, foods.preparation_time, foods.cooking_time, foods.calories_per_serving, foods.instructions, foods.image_url, foods.created_at, users.fname, users.lname
    FROM foods
    JOIN users ON foods.created_by = users.user_id
    ORDER BY foods.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$recipes = $result->fetch_all(MYSQLI_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Link | Recipe Management</title>
    <link rel="stylesheet" href="../assets/css/admin-1.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/recipe_management.css">
    
    <script src="./static/scripts/nav.js" defer></script>
    <script src="../assets/js/countries.js" defer></script>
    <script src="../assets/js/recipe.js" defer></script>

    <link rel="icon" type="image/png" sizes="32x32" href="./static/images/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div><a href="./index.html"><img class="logo" src="../assets/images/logo.png" alt="website logo"></a></div>
        <nav>
            <ul class="menu-items">
                <li><a href="./index.html" class="menu-item">Home</a></li>
                <li><a href="./login.html" class="menu-item">Login</a></li>
                <li><a href="./sign-in.html" class="menu-item">Sign-in</a></li>
                <li><a href="./dashboard.html" class="menu-item">Dashboard</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="admin-body">
        <div class="heading-section">
            <h2>Recipe Management</h2>
            <div class="navigation">
                <a href="./dashboard.html"><button class="btn">Dashboard</button></a>
                <a href="./users.html"><button class="btn">User Management</button></a>
                <a href="./recipe_management.html"><button class="btn-1">Recipe Management</button></a>
            </div>
        </div>

        <!-- Recipe Management Section -->
        <main>
            <section id="recipe-management">
                <h2>Manage Recipes</h2>

                <!-- Button to Open the Modal -->
                <button class="modal-button" id="openModalBtn">Add New Recipe</button>

                <!-- Recipe Table -->
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recipe-table-body">
                    <?php
                        // Loop through the fetched recipes and display them in the table
                        foreach ($recipes as $recipe) {
                            echo "<tr>";
                            echo "<td>{$recipe['food_id']}</td>";
                            echo "<td>{$recipe['name']}</td>";
                            echo "<td>{$recipe['fname']} {$recipe['lname']}</td>";
                            echo "<td>{$recipe['created_at']}</td>";
                            echo "<td>
                                    <button class='view-btn' data-toggle='modal' data-target='#viewModal' onclick='viewRecipe({$recipe['food_id']})'>View</button>
                                    <button class='edit-btn' data-toggle='modal' data-target='#editModal' onclick='editRecipe({$recipe['food_id']})'>Edit</button>
                                    <button class='delete-btn' onclick='deleteRecipe({$recipe['food_id']})'>Delete</button>
                                </td>";
                            echo "</tr>";
                        }
                    ?>
                    </tbody>
                </table>
                
            </section>
        </main>

        <!-- Modal Structure for New Recipe -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span> <!-- Close button -->

                <h2 class="modal-title">Add a New Recipe</h2>
                <form id="create-recipe-form" enctype="multipart/form-data" method="POST" action="recipe.php">
                    <!-- Recipe Form Fields -->
                    <div class="section-1">
                        <div><input type="text" name="recipe-name" id="recipe-name" placeholder="Enter recipe title" required></div>
                        <div><input type="text" name="first-name" id="first-name" placeholder="Enter first name" required></div>
                        <div><input type="text" name="last-name" id="last-name" placeholder="Enter last name" required></div>
                        <div><input type="date" name="date" id="date" required></div>

                        <div>
                            <select id="countrySelect" name="countrySelect" required>
                                <option value="">Choose Country</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-2">
                        <div class="text-area-1">
                            <div><textarea id="ingredient-list" name="ingredient-list" style="resize: none;" placeholder="Ingredient Name: Quantity Unit (optional)" required></textarea></div>
                        </div>

                        <div class="inner-section">
                            <div class="text-area-2">
                                <textarea id="allergies" name="allergies"  style="resize: none;" placeholder="Allergies information" required></textarea>
                            </div>

                            <div>
                                <input type="number" name="shelf-life" id="shelf-life" placeholder="Shelf Life (in days)" required>
                            </div>
                        </div>
                    </div>

                    <div class="section-3">
                        <div><input type="file" name="file" id="file" required></div>
                        <div><input type="number" name="prep-time" id="prep-time" placeholder="Preparation time (in minutes)" required></div>
                        <div><input type="number" name="cook-time" id="cook-time" placeholder="Cook time (in minutes)" required></div>
                        <div><input type="number" name="calories" id="calories" placeholder="Calories per serving" required></div>

                        <div><textarea name="instructions" id="instructions" style="resize: none;" placeholder="Instructions" required></textarea></div>
                    </div>

                    <div class="form-btns">
                        <button type="submit" id="submit-btn" class="submit">Submit</button>
                        <button type="button" class="close-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


   <!-- Recipe Modal -->

    <div id="recipeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRecipeModal()">&times;</span>
            <h3>Recipe Details</h3>
            <p id="modalRecipeDetails">Loading...</p>
        </div>
    </div>



  
    <!-- Edit Recipe Modal -->
    <div id="editRecipeModal" style="display:none;" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditRecipeModal()">&times;</span>
            <form id="editRecipeForm" method="POST">
                <label for="editRecipeId">Recipe ID</label>
                <div><input type="text" id="editRecipeId" name="id" readonly></div>

                <label for="editFoodId">Food ID</label>
                <div><input type="text" id="editFoodId" name="food_id"></div>

                <label for="editIngredientId">Ingredient ID</label>
                <div><input type="text" id="editIngredientId" name="ingredient_id"></div>

                <label for="editQuantity">Quantity</label>
                <div><input type="number" id="editQuantity" name="quantity"></div>

                <label for="editUnit">Unit</label>
                <div><input type="text" id="editUnit" name="unit"></div>

                <label for="editOptional">Optional</label>
                <div><input type="checkbox" id="editOptional" name="optional" value="1"></div>

                <button type="button" onclick="updateRecipe()">Update Recipe</button>
            </form>
        </div>
    </div>




    <script>
        // Get all the required DOM elements and functionality for the modal
        const openModalBtn = document.getElementById('openModalBtn');
        const modal = document.getElementById('myModal');
        const closeModalBtn = document.getElementById('closeModal');

        openModalBtn.onclick = function () {
            modal.style.display = "block";
        }

        closeModalBtn.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
