<?php
// Database connection and common helpers

// Harden session cookies against XSS theft (HttpOnly prevents JS access) and interception (Secure over HTTPS)
// These must be set before session_start() to take effect.
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_set_cookie_params([
    'httponly' => true,
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax',
]);

session_start();

// Defense-in-depth: Content Security Policy restricts inline/external script execution
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; form-action 'self'; base-uri 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");

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

// Escape output for HTML to prevent XSS: converts special characters to HTML entities
// so user-supplied data is rendered as text, not executed as HTML/JavaScript.
// Apply this to every value echoed from the database or user input.
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

// Format datetime in 12-hour format with AM/PM (Y-m-d h:i:s A)
function formatDateTime($datetime)
{
    if (!$datetime) return 'N/A';
    $dt = new DateTime($datetime);
    return $dt->format('Y-m-d h:i:s A');
}

// Validate password strength: minimum 8 characters, at least one uppercase, one lowercase, one number
function validatePasswordStrength($password)
{
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password) === 1;
}