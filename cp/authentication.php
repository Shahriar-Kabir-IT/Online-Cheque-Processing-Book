<?php
require_once '../config.php';
require_once '../session.php'; // Start session first!
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/admin/AdminModel.php';

// Allow both email and username login
$login = Validation::getPost('email'); // Field name is 'email' but accepts username too
$password = Validation::getPost('password');

if (empty($login) || empty($password)) {
    header('Location: login.php?error=1');
    exit();
}

$dao = new DataAccess();
$Admin = new AdminModel($dao);

$admins = $Admin->ListAll("oa_login = ? AND oa_active = ?", [$login, 1]);

if (empty($admins)) {
    header('Location: login.php?error=1');
    exit();
}

$adminData = $admins[0];
$passwordValid = false;

// Try modern password hash first
if (password_verify($password, $adminData['oa_password'])) {
    $passwordValid = true;
} 
// Fallback to plain text for legacy passwords (with trimming for safety)
elseif (trim($adminData['oa_password']) === trim($password)) {
    $passwordValid = true;
    // Update to hashed password
    $Admin->oa_id = $adminData['oa_id'];
    $Admin->oa_login = $adminData['oa_login'];
    $Admin->oa_password = password_hash($password, PASSWORD_DEFAULT);
    $Admin->oa_name = $adminData['oa_name'];
    $Admin->oa_department = $adminData['oa_department'];
    $Admin->oa_active = $adminData['oa_active'];
    $Admin->Save();
}

if ($passwordValid) {
    $_SESSION['oa_id'] = $adminData['oa_id'];
    $_SESSION['oa_login'] = $adminData['oa_login'];
    $_SESSION['oa_name'] = $adminData['oa_name'];
    $_SESSION['oa_department'] = $adminData['oa_department'];
    
    // Update last login
    $Admin->oa_id = $adminData['oa_id'];
    $Admin->oa_login = $adminData['oa_login'];
    $Admin->oa_password = $adminData['oa_password'];
    $Admin->oa_name = $adminData['oa_name'];
    $Admin->oa_department = $adminData['oa_department'];
    $Admin->oa_last_login = date('Y-m-d H:i:s');
    $Admin->oa_active = $adminData['oa_active'];
    
    $dao->Execute("UPDATE ocps_admin SET oa_last_login = NOW() WHERE oa_id = ?", [$adminData['oa_id']]);
    
    header('Location: index.php');
    exit();
} else {
    header('Location: login.php?error=1');
    exit();
}
