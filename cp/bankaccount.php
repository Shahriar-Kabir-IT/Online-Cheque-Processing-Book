<?php
$pageTitle = 'Bank Accounts';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/account/AccountModel.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Account = new AccountModel($dao);

$accounts = $Account->ListAllAccount('', [], 'a.sl', 'DESC');
?>
<div class="page-content">
    <h2>Bank Accounts</h2>
    
    <div class="page-actions">
        <a href="new_account.php" class="btn btn-primary">New Account</a>
    </div>
    
    <?php if (empty($accounts)): ?>
        <p class="no-data">No accounts found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Account Number</th>
                    <th>Account Code</th>
                    <th>Company</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($acc['acc_number']); ?></td>
                        <td><?php echo htmlspecialchars($acc['ac_code'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($acc['company_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($acc['bank_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($acc['branch'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($acc['ac_type'] ?? 'N/A'); ?></td>
                        <td><?php echo formatCurrency($acc['balance']); ?></td>
                        <td>
                            <a href="view_accountbalance.php?acc=<?php echo urlencode($acc['acc_number']); ?>" class="btn btn-sm btn-primary">View Balance</a>
                            <a href="view_accountdetails.php?acc=<?php echo urlencode($acc['acc_number']); ?>" class="btn btn-sm btn-secondary">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
