<?php
require_once __DIR__ . '/config.php';

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Update last activity time on each request
if (isset($_SESSION['oa_id'])) {
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
    
    // Check if session has expired (30 minutes of inactivity)
    $inactive_time = time() - $_SESSION['last_activity'];
    if ($inactive_time > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        // Return JSON for AJAX requests, otherwise redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'timeout', 'redirect' => 'cp/login.php?msg=timeout']);
            exit();
        }
        header("Location: cp/login.php?msg=timeout");
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

// Session timeout handling (legacy support)
if (isset($_SESSION['start'])) {
    $session_life = time() - $_SESSION['start'];
    if ($session_life > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'timeout', 'redirect' => 'cp/login.php?msg=timeout']);
            exit();
        }
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

