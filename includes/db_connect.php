<?php
// Database connection and common helpers

session_start();

// Dynamic base URL detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = $protocol . $host . ($scriptDir === '/' ? '/' : rtrim($scriptDir, '/') . '/');
define('BASE_URL', $baseUrl);

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
?>
