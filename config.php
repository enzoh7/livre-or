<?php
date_default_timezone_set('Europe/Paris');

define('DB_HOST', 'localhost');
define('DB_NAME', 'livreor');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDbConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $offset = date('P');
        $pdo->exec("SET time_zone = '$offset'");
        return $pdo;
    } catch(PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}
?>