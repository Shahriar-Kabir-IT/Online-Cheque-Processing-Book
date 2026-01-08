<?php
require_once __DIR__ . '/config.php';

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Session timeout handling
if (isset($_SESSION['start'])) {
    $session_life = time() - $_SESSION['start'];
    if ($session_life > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header("Location: cp/login.php?msg=timeout");
        exit();
    }
} else {
    $_SESSION['start'] = time();
}

// Session regeneration every 30 minutes
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['oa_id']) && !empty($_SESSION['oa_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

