<?php
session_start();
require_once '../../db/db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch user role and ID from the session
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role']; // Assuming roles are defined as 1 (Super Admin) and 2 (Regular Admin)

// Initialize variables
$totalUsers = 0;
$totalRecipes = 0;
$pendingApprovals = 0;
$users = [];
$recentRecipes = [];
$myRecipes = [];

$limit = 5; // For recently added recipes and users

// Fetch data based on user role
if ($userRole == 1) { // Super Admin
    // Fetch analytics data
    $query = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $row = $query->fetch_assoc();
    $totalUsers = $row['total_users'];

    $query = $conn->query("SELECT COUNT(*) AS total_recipes FROM recipes");
    $row = $query->fetch_assoc();
    $totalRecipes = $row['total_recipes'];

    $query = $conn->query("SELECT COUNT(*) AS pending_approvals FROM foods WHERE is_approved = 'pending'");
    $row = $query->fetch_assoc();
    $pendingApprovals = $row['pending_approvals'];

    // Fetch user data for User Management with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT user_id, CONCAT(fname, ' ', lname) AS full_name, email, role, created_at FROM users LIMIT ?, ?");
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    // Fetch recently added recipes
    $query = $conn->query("SELECT * FROM foods ORDER BY created_at DESC LIMIT $limit");
    $recentRecipes = $query->fetch_all(MYSQLI_ASSOC);

} elseif ($userRole == 2) { // Regular Admin

    // Fetch total recipes created by the user from the foods table
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_recipes FROM foods WHERE created_by = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalRecipes = $row['total_recipes'];

    // Fetch recent food items added by the user (limit to the latest 5)
    $stmt = $conn->prepare("SELECT * FROM foods WHERE created_by = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param('ii', $userId, $limit);
    $stmt->execute();
    $recentRecipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch all food items created by the user for management (with a limit for large data sets)
    $stmt = $conn->prepare("SELECT * FROM foods WHERE created_by = ? LIMIT ?");
    $stmt->bind_param('ii', $userId, $limit);
    $stmt->execute();
    $myRecipes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Kitchen Link</title>
    <link rel="stylesheet" href="../../assets/css/nav.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <script src="../../assets/scripts/nav.js" defer></script>
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/images/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <!-- logo -->
        <div> <a href="../../index.php"><img class="logo" src="../../assets/images/logo.png" alt="website logo"></a></div>
        <!-- End of logo -->


        <!-- Hamburger -->
        <div class="hamburger">
            <div class="menu-btn">
                <div class="menu-btn_lines"></div>
            </div>
        </div>
        <!-- End of Hamburger -->
        


      <!-- navigation links -->
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="../../index.php" class="menu-item">Home</a></li>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="../login.php" class="menu-item">Login</a></li>
                        <li><a href="../sign-in.php" class="menu-item">Sign-in</a></li>
                    <?php else: ?>
                        <li><button class="sign-in-btn"><a href="../admin/dashboard.php" class="menu-item">Dashboard</a></button></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- End of Navigation section -->

    <header>
        <div class="header-content">
            <div><h1 class="heading">Welcome to the Dashboard</h1></div>
            <div>
                <p>Hello, <?php echo htmlspecialchars($_SESSION['first_name']) ." ". htmlspecialchars($_SESSION['last_name']); ?></p>
                <a href="../logout.php">Logout</a>
            </div>
            
        </div>

       
    </header>

    <!-- Super Admin Section -->
    <?php if ($userRole == 1): ?>
        <div class="admin-container">
            <div class="analytics-section">
                <h2>Analytics Section</h2>
                <p>Total Users: <?php echo $totalUsers; ?></p>
                <p>Total Recipes: <?php echo $totalRecipes; ?></p>
                <p>Pending User Approvals: <?php echo $pendingApprovals; ?></p>
            </div>

            <div class="user-management-section">
                <h2>User Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <button class="delete-btn" onclick="deleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <div class="recipe-overview">
                <h2>Recipe Overview</h2>
                <h3>Recently Added Recipes</h3>
                <ul>
                    <?php foreach ($recentRecipes as $recipe): ?>
                        <li><?php echo htmlspecialchars($recipe['name']); ?> (Created on <?php echo htmlspecialchars($recipe['created_at']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
                <p>Total Recipe Count: <?php echo $totalRecipes; ?></p>
            </div>

        </div>
        
    <!-- Regular Admin Section -->
    <?php elseif ($userRole == 2): ?>
        <div class="personal-container">
        <div class="personal-analytics-section">
            <h2>Personal Analytics Section</h2>
            <p>Total Recipes Added: <?php echo $totalRecipes; ?></p>
        </div>

        <div class="recipe-management-section">
            <h2>My Recipe Management</h2>
            <h3>Recently Added Recipes</h3>
            <ul>
                <?php foreach ($recentRecipes as $recipe): ?>
                    <li><?php echo htmlspecialchars($recipe['name']); ?> (Created on <?php echo htmlspecialchars($recipe['created_at']); ?>)</li>
                <?php endforeach; ?>
            </ul>

            <h3>All My Recipes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Recipe Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myRecipes as $myRecipe): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($myRecipe['name']); ?></td>
                        <td><?php echo htmlspecialchars($myRecipe['is_approved']); ?></td>
                        <td>
                            <button class="delete-btn" onclick="deleteRecipe(<?php echo $myRecipe['food_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
        
    <?php endif; ?>

    <script>
    function deleteUser(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            fetch('../../actions/delete_user.php', {
                method: 'DELETE',
                body: new URLSearchParams({ id: userId }) // Send the user ID in the body
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted successfully');
                    location.reload(); // Refresh the table
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }


    function deleteRecipe(foodId) {
    if(confirm("Are you sure you want to delete this recipe?")){
        fetch(`../../actions/delete_recipe.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${foodId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>

</body>
</html>
