<?php
// Database connection and common helpers

session_start();

// Dynamic base URL detection - always point to project root
$scriptName = $_SERVER['SCRIPT_NAME'];
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$basePath = '/' . ltrim(str_replace($docRoot, '', $projectRoot), '/');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . $host . ($basePath === '/' ? '/' : rtrim($basePath, '/') . '/'));

$host = 'localhost';
$db   = 'pet_portrait_prints';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set a flash message
function setFlash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}

// Get and clear a flash message
function getFlash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

// Escape output for HTML
function escape($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Output a flash message as HTML
function flashHtml($key)
{
    $message = getFlash($key);
    if ($message === null) {
        return '';
    }
    $type = ($key === 'error') ? 'flash-error' : 'flash-success';
    return '<div class="flash ' . $type . '">' . escape($message) . '</div>';
}

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Get current user ID
function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

// Get current username
function currentUsername()
{
    return $_SESSION['username'] ?? null;
}

// Get current user full name
function getCurrentUserName()
{
    if (isset($_SESSION['first_name'], $_SESSION['last_name'])) {
        return $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    }
    return null;
}

// Require authentication - redirect to login if not logged in
function requireAuth()
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

// Check if current user is admin
function isAdmin()
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Require admin role
function requireAdmin()
{
    requireAuth();
    if (!isAdmin()) {
        setFlash('error', 'Admin access required.');
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}