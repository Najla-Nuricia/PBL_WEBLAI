<?php
session_start();

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Language switcher
if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'id' || $_GET['lang'] === 'en') {
        $_SESSION['lang'] = $_GET['lang'];
    }
    // Redirect to the same page without the lang parameter
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $uri);
    exit;
}

// Load language file
$lang = require_once __DIR__ . '/' . $_SESSION['lang'] . '.php';

// Function to get translated string
function __($key, $placeholders = [])
{
    global $lang;
    $text = $lang[$key] ?? $key;

    foreach ($placeholders as $placeholder => $value) {
        $text = str_replace(":$placeholder", $value, $text);
    }

    return $text;
}