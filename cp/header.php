<?php
require_once '../session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navi.css">
    <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src="../js/session-timeout.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-container">
                    <img src="../img/logo.png" alt="ANANTA OCPB Logo">
                </div>
                <h1><?php echo APP_NAME; ?></h1>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['oa_name'] ?? 'User'); ?></span>
                <span class="user-dept"><?php echo htmlspecialchars($_SESSION['oa_department'] ?? ''); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="new_cheque.php">New Cheque</a></li>
            <li><a href="pending_cheques.php">Pending Cheques</a></li>
            <li><a href="printed_cheques_company.php">Printed Cheques</a></li>
            <li><a href="cancelled_cheques.php">Cancelled Cheques</a></li>
            <li><a href="new_account.php">New Account</a></li>
            <li><a href="bankaccount.php">Bank Accounts</a></li>
            <li><a href="new_adjustment.php">Adjustments</a></li>
            <li><a href="manage_beneficiary.php">Beneficiaries</a></li>
            <li><a href="report.php">Reports</a></li>
        </ul>
    </nav>
    <main class="main-content">
