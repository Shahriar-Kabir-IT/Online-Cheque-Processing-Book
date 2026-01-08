<?php
$pageTitle = 'Account Balance';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/account/AccountModel.php';
require_once '../class/common/Validation.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$acc = Validation::getGet('acc', '');

if (empty($acc)) {
    header('Location: bankaccount.php');
    exit();
}

$Account = new AccountModel($dao);
$accounts = $Account->ListAllAccount("a.acc_number = ?", [$acc]);

if (empty($accounts)) {
    header('Location: bankaccount.php');
    exit();
}

$account = $accounts[0];
?>
<div class="page-content">
    <h2>Account Balance</h2>
    
    <div class="account-info">
        <h3>Account Information</h3>
        <table class="info-table">
            <tr>
                <th>Account Number:</th>
                <td><?php echo htmlspecialchars($account['acc_number']); ?></td>
            </tr>
            <tr>
                <th>Company:</th>
                <td><?php echo htmlspecialchars($account['company_name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Bank:</th>
                <td><?php echo htmlspecialchars($account['bank_name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Branch:</th>
                <td><?php echo htmlspecialchars($account['branch'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Account Type:</th>
                <td><?php echo htmlspecialchars($account['ac_type'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Current Balance:</th>
                <td><strong><?php echo formatCurrency($account['balance']); ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div class="form-actions">
        <a href="view_accountdetails.php?acc=<?php echo urlencode($acc); ?>" class="btn btn-primary">View Transaction Details</a>
        <a href="bankaccount.php" class="btn btn-secondary">Back</a>
    </div>
</div>
<?php require_once 'footer.php'; ?>
