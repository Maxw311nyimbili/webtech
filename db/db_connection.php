<?php
$servername = "localhost";
$username = "maxwell.nyimbili"; 
$password = "L3gendary1864"; 
$dbname = "webtech_fall2024_maxwell_nyimbili";
$port = "3341";

// Attempt to connect to the database
$pdo = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($pdo -> connect_error) 
{
    echo''. $pdo -> connect_error;
    die("Connection failed: " . $pdo->connect_error);
}
?>