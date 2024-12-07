<?php
session_start();
require 'database_connect.php';

// Database connection
function getDatabaseConnection() {
    global $pdo; // Use the $pdo variable initialized in database_connect.php
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    die("Failed to access the database connection.");
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if user is not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Check if user is an admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect to login if user is not an admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: login.php");
        exit();
    }
}

// Utility function to fetch all rows from a query
function fetchAll($query, $params = []) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Utility function to fetch a single row
function fetchOne($query, $params = []) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch();
}

// Utility function to execute a query
function executeQuery($query, $params = []) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}
?>
