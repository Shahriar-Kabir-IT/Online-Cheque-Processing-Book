<?php
/**
 * Debug script to test login credentials
 */

require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/admin/AdminModel.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #d1fae5; padding: 15px; border-radius: 6px; margin: 10px 0; border-left: 4px solid #10b981; }
        .error { background: #fee2e2; padding: 15px; border-radius: 6px; margin: 10px 0; border-left: 4px solid #ef4444; }
        .info { background: #dbeafe; padding: 15px; border-radius: 6px; margin: 10px 0; border-left: 4px solid #3b82f6; }
        pre { background: #f8fafc; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Login Authentication Test</h1>";

try {
    // Test database connection
    echo "<div class='info'><strong>Step 1:</strong> Testing database connection...</div>";
    $dao = new DataAccess();
    echo "<div class='success'>✓ Database connection successful!</div>";
    
    // Test user lookup
    echo "<div class='info'><strong>Step 2:</strong> Looking up user 'amin'...</div>";
    $Admin = new AdminModel($dao);
    $login = 'amin';
    $password = 'asl';
    
    $admins = $Admin->ListAll("oa_login = ? AND oa_active = ?", [$login, 1]);
    
    if (empty($admins)) {
        echo "<div class='error'>
            <strong>✗ User not found!</strong><br>
            Login: '{$login}'<br>
            Active: 1<br><br>
            Checking all users...
        </div>";
        
        // Show all users
        $allUsers = $Admin->ListAll('', [], 'oa_id', 'ASC');
        echo "<div class='info'><strong>All users in database:</strong></div>";
        echo "<pre>";
        foreach ($allUsers as $u) {
            echo "ID: {$u['oa_id']}, Login: '{$u['oa_login']}', Active: {$u['oa_active']}, Password: '{$u['oa_password']}'\n";
        }
        echo "</pre>";
    } else {
        $adminData = $admins[0];
        echo "<div class='success'>
            <strong>✓ User found!</strong><br>
            ID: {$adminData['oa_id']}<br>
            Login: '{$adminData['oa_login']}'<br>
            Name: '{$adminData['oa_name']}'<br>
            Active: {$adminData['oa_active']}<br>
            Password in DB: '{$adminData['oa_password']}'<br>
            Password length: " . strlen($adminData['oa_password']) . "
        </div>";
        
        // Test password verification
        echo "<div class='info'><strong>Step 3:</strong> Testing password verification...</div>";
        
        $passwordValid = false;
        $method = '';
        
        // Try hashed password first
        if (password_verify($password, $adminData['oa_password'])) {
            $passwordValid = true;
            $method = 'Hashed password verification';
        } 
        // Try plain text
        elseif ($adminData['oa_password'] === $password) {
            $passwordValid = true;
            $method = 'Plain text comparison';
        }
        // Try trimmed comparison
        elseif (trim($adminData['oa_password']) === trim($password)) {
            $passwordValid = true;
            $method = 'Trimmed plain text comparison';
        }
        
        if ($passwordValid) {
            echo "<div class='success'>
                <strong>✓ Password is VALID!</strong><br>
                Method: {$method}<br>
                You should be able to login successfully.
            </div>";
        } else {
            echo "<div class='error'>
                <strong>✗ Password is INVALID!</strong><br>
                Expected: '{$adminData['oa_password']}'<br>
                Provided: '{$password}'<br>
                Expected length: " . strlen($adminData['oa_password']) . "<br>
                Provided length: " . strlen($password) . "<br>
                <br>
                <strong>Debug Info:</strong><br>
                Expected bytes: " . bin2hex($adminData['oa_password']) . "<br>
                Provided bytes: " . bin2hex($password) . "
            </div>";
        }
    }
    
    // Test session
    echo "<div class='info'><strong>Step 4:</strong> Testing session...</div>";
    require_once '../session.php';
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<div class='success'>✓ Session is active</div>";
    } else {
        echo "<div class='error'>✗ Session is not active</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>
        <strong>Error:</strong><br>
        " . htmlspecialchars($e->getMessage()) . "<br>
        <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
    </div>";
}

echo "</div></body></html>";
