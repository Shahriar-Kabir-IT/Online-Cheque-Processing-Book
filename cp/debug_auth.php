<?php
/**
 * Debug authentication - shows what's happening step by step
 * Remove this file after fixing the issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once '../session.php';
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/admin/AdminModel.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Auth Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .step { background: white; padding: 15px; margin: 10px 0; border-radius: 6px; border-left: 4px solid #3b82f6; }
        .success { border-left-color: #10b981; background: #d1fae5; }
        .error { border-left-color: #ef4444; background: #fee2e2; }
        pre { background: #f8fafc; padding: 10px; border-radius: 4px; overflow-x: auto; margin: 5px 0; }
        .test-form { background: white; padding: 20px; border-radius: 6px; margin: 20px 0; }
        input { padding: 8px; margin: 5px; width: 200px; }
        button { padding: 8px 15px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Authentication Debug Tool</h1>
    
    <div class='test-form'>
        <h3>Test Login</h3>
        <form method='POST'>
            <input type='text' name='test_login' placeholder='Username' value='amin'><br>
            <input type='password' name='test_password' placeholder='Password' value='asl'><br>
            <button type='submit'>Test Authentication</button>
        </form>
    </div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testLogin = $_POST['test_login'] ?? '';
    $testPassword = $_POST['test_password'] ?? '';
    
    echo "<div class='step'><strong>Step 1:</strong> Received credentials<br>
        Login: '{$testLogin}'<br>
        Password: '{$testPassword}'<br>
        Login length: " . strlen($testLogin) . "<br>
        Password length: " . strlen($testPassword) . "
    </div>";
    
    try {
        $dao = new DataAccess();
        echo "<div class='step success'><strong>Step 2:</strong> Database connection successful</div>";
        
        $Admin = new AdminModel($dao);
        
        echo "<div class='step'><strong>Step 3:</strong> Searching for user...<br>
            Query: oa_login = '{$testLogin}' AND oa_active = 1
        </div>";
        
        $admins = $Admin->ListAll("oa_login = ? AND oa_active = ?", [$testLogin, 1]);
        
        if (empty($admins)) {
            echo "<div class='step error'>
                <strong>Step 4:</strong> User NOT found!<br>
                Checking all users in database...
            </div>";
            
            $allUsers = $Admin->ListAll('', [], 'oa_id', 'ASC');
            echo "<div class='step'><strong>All users:</strong><pre>";
            foreach ($allUsers as $u) {
                $match = ($u['oa_login'] === $testLogin) ? ' ← MATCHES!' : '';
                echo "ID: {$u['oa_id']}, Login: '{$u['oa_login']}' (len: " . strlen($u['oa_login']) . "), Active: {$u['oa_active']}{$match}\n";
            }
            echo "</pre></div>";
        } else {
            $adminData = $admins[0];
            echo "<div class='step success'>
                <strong>Step 4:</strong> User FOUND!<br>
                ID: {$adminData['oa_id']}<br>
                Login: '{$adminData['oa_login']}'<br>
                Name: '{$adminData['oa_name']}'<br>
                Active: {$adminData['oa_active']}<br>
                Password in DB: '{$adminData['oa_password']}'<br>
                Password DB length: " . strlen($adminData['oa_password']) . "<br>
                Password DB hex: " . bin2hex($adminData['oa_password']) . "
            </div>";
            
            echo "<div class='step'><strong>Step 5:</strong> Testing password verification...</div>";
            
            // Test 1: Hashed
            $hashTest = password_verify($testPassword, $adminData['oa_password']);
            echo "<div class='step'>Hash verification: " . ($hashTest ? '✓ PASS' : '✗ FAIL') . "</div>";
            
            // Test 2: Exact match
            $exactTest = ($adminData['oa_password'] === $testPassword);
            echo "<div class='step'>Exact match: " . ($exactTest ? '✓ PASS' : '✗ FAIL') . "<br>
                DB: '{$adminData['oa_password']}'<br>
                Input: '{$testPassword}'<br>
                DB hex: " . bin2hex($adminData['oa_password']) . "<br>
                Input hex: " . bin2hex($testPassword) . "
            </div>";
            
            // Test 3: Trimmed match
            $trimTest = (trim($adminData['oa_password']) === trim($testPassword));
            echo "<div class='step'>Trimmed match: " . ($trimTest ? '✓ PASS' : '✗ FAIL') . "</div>";
            
            // Test 4: Case insensitive
            $caseTest = (strtolower($adminData['oa_password']) === strtolower($testPassword));
            echo "<div class='step'>Case insensitive: " . ($caseTest ? '✓ PASS' : '✗ FAIL') . "</div>";
            
            $passwordValid = $hashTest || $exactTest || $trimTest;
            
            if ($passwordValid) {
                echo "<div class='step success'>
                    <strong>Step 6:</strong> Password is VALID!<br>
                    Session will be created...
                </div>";
                
                $_SESSION['oa_id'] = $adminData['oa_id'];
                $_SESSION['oa_login'] = $adminData['oa_login'];
                $_SESSION['oa_name'] = $adminData['oa_name'];
                $_SESSION['oa_department'] = $adminData['oa_department'];
                
                echo "<div class='step success'>
                    <strong>Step 7:</strong> Session variables set!<br>
                    oa_id: {$_SESSION['oa_id']}<br>
                    oa_login: {$_SESSION['oa_login']}<br>
                    oa_name: {$_SESSION['oa_name']}<br>
                    <a href='index.php'>→ Go to Dashboard</a>
                </div>";
            } else {
                echo "<div class='step error'>
                    <strong>Step 6:</strong> Password is INVALID!<br>
                    All verification methods failed.
                </div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='step error'>
            <strong>ERROR:</strong><br>
            " . htmlspecialchars($e->getMessage()) . "<br>
            <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
        </div>";
    }
}

echo "</body></html>";
