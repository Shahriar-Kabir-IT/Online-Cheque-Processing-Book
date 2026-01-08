<?php
/**
 * AJAX endpoint to refresh session on user activity
 */
require_once '../session.php';

if (isLoggedIn()) {
    // Update last activity time
    $_SESSION['last_activity'] = time();
    $_SESSION['start'] = time();
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Session refreshed',
        'time_remaining' => SESSION_TIMEOUT
    ]);
} else {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'status' => 'timeout',
        'redirect' => 'login.php?msg=timeout'
    ]);
}
