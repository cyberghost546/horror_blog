<?php 
$host = 'localhost'; 
$db = 'silent_evidence'; 
$user = 'root'; // change if needed 
$pass = ''; // change if needed 

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4"; 
$options = [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    ]; 
    try { 
        $pdo = new PDO($dsn, $user, $pass, $options); 
    } 
    catch (PDOException $e) { die('Database connection failed'); } 
?>