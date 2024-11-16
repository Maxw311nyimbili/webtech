<?php
require '../db/db_connection.php';


if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    try {
        $query = $pdo->prepare("SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users WHERE user_id = ?");
        $query->execute([$userId]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Link | User Management</title>
    <link rel="stylesheet" href="../assets/css/admin-1.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/recipe_management.css">
    <script src="../assets/js/nav.js" defer></script>
    <script src="../assets/js/users.js" defer></script>
    <link rel="icon" type="image/png" sizes="32x32" href="./static/images/favicon-32x32.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Navigation Section -->
    <header class="header">
        <div><a href="./index.html"><img class="logo" src="../assets/images/logo.png" alt="website logo"></a></div>
        <div class="hamburger">
            <div class="menu-btn"><div class="menu-btn_lines"></div></div>
        </div>
        <nav>
            <div class="nav_links">
                <ul class="menu-items">
                    <li><a href="./index.html" class="menu-item">Home</a></li>
                    <li><a href="./login.html" class="menu-item">Login</a></li>
                    <li><a href="./sign-in.html" class="menu-item">Sign-in</a></li>
                    <li><button class="sign-in-btn"><a href="./dashboard.html" class="menu-item">Dashboard</a></button></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <div class="admin-body">
        <div class="heading-section">
            <div class="admin-heading"><h2>User Management</h2></div>
            <div class="navigation">
                <li><a href="./dashboard.html"><button class="btn">Dashboard</button></a></li>
                <li><a href="./users.html"><button class="btn">User Management</button></a></li>
                <li><a href="./recipe_management.html"><button class="btn-1">Recipe Management</button></a></li>
            </div>
        </div>

        <main>
            <section id="user-management">
                <h2 class="graph-heading">Manage Users</h2>
                <br>
                <button id="addUserBtn" class="submit-btn" onclick="openUserModal()">Add New User</button>
                <br>
                <br>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = $pdo->query("SELECT user_id, CONCAT(fname, ' ', lname) AS name, email FROM users");
                            $result = $query->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative array
                            
                            if (count($result) > 0) {
                                foreach ($result as $row) {
                                    echo "<tr>
                                            <td>{$row['user_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['email']}</td>
                                            <td>
                                                <button onclick='viewUser({$row['user_id']})'>View</button>
                                                <button onclick='editUser({$row['user_id']})'>Edit</button>
                                                <button onclick='deleteUser({$row['user_id']})'>Delete</button>
                                            </td>
                                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No users found.</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>

        <!-- Add User Modal -->
        <div id="addUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeUserModal()">&times;</span>
                <h3>Add New User</h3>
                <form id="addUserForm" onsubmit="addUser(event)">
                    <label for="newFirstName">First Name:</label>
                    <div><input type="text" id="newFirstName" name="newFirstName" required></div>
                    

                    <label for="newLastName">Last Name:</label>
                    <div><input type="text" id="newLastName" name="newLastName" required></div>
                    

                    <label for="newEmail">Email:</label>
                    <div><input type="email" id="newEmail" name="newEmail" required></div>
                    

                    <label for="newPassword">Password:</label>
                    <div><input type="text" id="newPassword" name="newPassword" value="kitchen1234" readonly></div>
                    

                    <label for="newRole">Role:</label>
                    <select id="newRole" name="newRole" required>
                        <option value="1">Admin</option>
                        <option value="2">Regular User</option>
                    </select>
                    <br>
                    <br>

                    <button type="submit" class="submit-btn">Add User</button>
                </form>
                <p id="addErrorMessage" style="color: red; display: none;">Please fill in all required fields with valid information.</p>
            </div>
        </div>


        <!-- User Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3>User Details</h3>
                <p id="modalUserDetails">Loading...</p>
            </div>
        </div>


        <!-- Edit User Modal -->
        <div id="editUserModal" style="display:none;" class="modal  ">
          <div class="modal-content">
          <span class="close" onclick="closeEditModal()">&times;</span>
            <form id="editUserForm" method="POST">
                    <label for="editUserId">User ID</label>
                    <div><input type="text" id="editUserId" name="id" readonly></div>
                    

                    <label for="editUsername">Name</label>
                    <div><input type="text" id="editUsername" name="name"></div>
                    

                    <label for="editEmail">Email</label>
                    <div><input type="email" id="editEmail" name="email"></div> 
                    <br>
                    

                    <button type="button" onclick="updateUser()">Update</button>
                </form>
          </div>
        </div>


    </div>
</body>
</html>
