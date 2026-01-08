<?php
/**
 * Helper script to check existing users in the database
 * Run this after importing db.sql to see available login credentials
 */

require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/admin/AdminModel.php';

try {
    $dao = new DataAccess();
    $Admin = new AdminModel($dao);
    
    $users = $Admin->ListAll('', [], 'oa_id', 'ASC');
    
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Users Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8fafc; font-weight: 600; }
        .active { color: #10b981; font-weight: bold; }
        .inactive { color: #ef4444; }
        .warning { background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #f59e0b; }
        .info { background: #dbeafe; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Database Users Check</h1>";
    
    if (empty($users)) {
        echo "<div class='warning'>
            <strong>No users found in database!</strong><br>
            You need to create a user manually or import the db.sql file properly.
        </div>";
    } else {
            echo "<div class='info'>
            <strong>Found " . count($users) . " user(s) in the database.</strong><br>
            Use the credentials below to login. The system supports both hashed and plain text passwords.<br>
            <strong>Note:</strong> Plain text passwords will be automatically hashed on first login for security.
        </div>";
        
        // Show password if plain text
        echo "<div class='warning'>
            <strong>⚠️ Security Note:</strong> If passwords are shown below, they are stored as plain text in the database.<br>
            They will be automatically converted to secure hashes on first successful login.
        </div>";
        
        echo "<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email/Login</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Password Type</th>
                    <th>Status</th>
                    <th>Last Login</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($users as $user) {
            $passwordType = 'Unknown';
            $passwordPreview = substr($user['oa_password'], 0, 20) . '...';
            
            // Check if password is hashed
            if (preg_match('/^\$2[ayb]\$.{56}$/', $user['oa_password'])) {
                $passwordType = 'Hashed (bcrypt)';
                $passwordPreview = '***HASHED***';
            } elseif (strlen($user['oa_password']) < 50) {
                $passwordType = 'Plain Text';
                $passwordPreview = htmlspecialchars($user['oa_password']);
            }
            
            $statusClass = $user['oa_active'] == 1 ? 'active' : 'inactive';
            $statusText = $user['oa_active'] == 1 ? 'Active' : 'Inactive';
            
            $lastLogin = $user['oa_last_login'] ? date('d/m/Y H:i', strtotime($user['oa_last_login'])) : 'Never';
            
            $passwordDisplay = $passwordType === 'Plain Text' ? 
                "<strong style='color: #dc2626;'>" . htmlspecialchars($user['oa_password']) . "</strong>" : 
                $passwordPreview;
            
            echo "<tr>
                <td>{$user['oa_id']}</td>
                <td><strong>" . htmlspecialchars($user['oa_login']) . "</strong></td>
                <td>" . htmlspecialchars($user['oa_name']) . "</td>
                <td>" . htmlspecialchars($user['oa_department'] ?? 'N/A') . "</td>
                <td>{$passwordType}<br><small style='color: #64748b;'>{$passwordDisplay}</small></td>
                <td class='{$statusClass}'>{$statusText}</td>
                <td>{$lastLogin}</td>
            </tr>";
        }
        
        echo "</tbody></table>";
        
        echo "<div class='info' style='margin-top: 20px;'>
            <strong>Login Instructions:</strong><br>
            1. Go to <a href='login.php'>Login Page</a><br>
            2. Use the Email/Login from the table above<br>
            3. Use the password shown (if plain text) or your known password<br>
            4. The system will automatically hash plain text passwords on first login
        </div>";
    }
    
    echo "</div></body></html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .error { background: #fee2e2; padding: 15px; border-radius: 6px; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <div class='error'>
        <strong>Database Connection Error!</strong><br>
        " . htmlspecialchars($e->getMessage()) . "<br><br>
        Please check your database configuration in config.php
    </div>
</body>
</html>";
}
