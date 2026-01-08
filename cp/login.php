<?php
require_once '../session.php';
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1><?php echo APP_NAME; ?></h1>
                <p>Ananta Online Cheque Processing Book</p>
            </div>
            <?php if (isset($_GET['error']) || isset($_GET['msg'])): ?>
                <div class="alert alert-<?php echo isset($_GET['error']) ? 'error' : 'info'; ?>">
                    <?php 
                    if (isset($_GET['error'])) {
                        echo 'Invalid email or password. Please try again.';
                    } elseif (isset($_GET['msg']) && $_GET['msg'] === 'timeout') {
                        echo 'Your session has expired. Please login again.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="authentication.php" class="login-form">
                <div class="form-group">
                    <label for="email">Username / Email</label>
                    <input type="text" id="email" name="email" required autofocus placeholder="Enter username or email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
