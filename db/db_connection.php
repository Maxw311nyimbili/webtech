<?php
// $servername = "localhost";
// $username = "root"; 
// $password = ""; 
// $dbname = "recipe_sharing";
// $port = "3306";


// $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";


// $options = [
//     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
//     PDO::ATTR_EMULATE_PREPARES => false,
//     PDO::ATTR_TIMEOUT => 10
// ];

// try {
//     $pdo = new PDO($dsn, $username, $password, $options);

// } catch (PDOException $e) {
//     die('Could not connect to the database: ' . $e->getMessage());
// }

// try {
//     $pdo->query('SELECT 1');

// } catch (PDOException $e) {
//     die('Connection failed: ' . $e->getMessage());
// }


$servername = "localhost";
$username = "maxwell.nyimbili"; 
$password = "L3gendary1864"; 
$dbname = "webtech_fall2024_maxwell_nyimbili";
$port = "3341";

// // PDO connection settings
// $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";


// $options = [
//     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for error handling
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch results as associative arrays
//     PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation of prepared statements (for better security)
//     PDO::ATTR_TIMEOUT => 10
// ];

// try {
//     // Create the PDO instance (database connection)
//     $pdo = new PDO($dsn, $username, $password, $options);
//     echo "Connected to the database successfully"; // Optional success message
// } catch (PDOException $e) {
//     // Catch connection errors and display a message
//     die('Could not connect to the database: ' . $e->getMessage());
// }

// // Optional: Test the connection by executing a simple query
// try {
//     $pdo->query('SELECT 1');
//     echo 'Connection is successful';
// } catch (PDOException $e) {
//     die('Connection failed: ' . $e->getMessage());
// }


// <?php

// $db_host = 'localhost';
// $db_user = 'root';
// $db_pass = 'cs341webtech';
// $db_name = 'AC2024';

// Attempt to connect to the database
$pdo = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($pdo -> connect_error) 
{
    echo''. $pdo -> connect_error;
    die("Connection failed: " . $pdo->connect_error);
}
echo "Connection successful";

// $servername = "169.239.251.102";
// $username = "maxwell.nyimbili"; 
// $password = "L3gendary1864"; 
// $dbname = "webtech_fall2024_maxwell_nyimbili";
// $port = 3341;

// // Create a new mysqli instance for connection
// $conn = new mysqli($servername, $username, $password, $dbname, $port);

// // Check for connection errors
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error); // Print error if connection fails
// }

// echo "Connected to the database successfully"; // Success message

// // Optional: Test the connection by executing a simple query
// $sql = "SELECT 1";
// if ($conn->query($sql) === TRUE) {
//     echo 'Connection is successful';
// } else {
//     die('Connection failed: ' . $conn->error); // Display the error message if query fails
// }

// // Close the connection when done
// $conn->close();

?>