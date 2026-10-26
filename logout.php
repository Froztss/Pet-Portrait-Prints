<?php
require_once __DIR__ . '/includes/db_connect.php';

session_destroy();
header('Location: ' . BASE_URL . 'login.php');
exit;