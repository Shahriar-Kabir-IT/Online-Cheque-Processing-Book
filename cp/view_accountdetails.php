<?php
$pageTitle = 'Account Details';
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

// Get date range (default: last 30 days)
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));

$details = $Account->getaccdetails($acc, $fromDate, $toDate);
?>
<div class="page-content">
    <h2>Account Transaction Details</h2>
    
    <div class="account-info">
        <p><strong>Account:</strong> <?php echo htmlspecialchars($account['acc_number']); ?> | 
           <strong>Company:</strong> <?php echo htmlspecialchars($account['company_name'] ?? 'N/A'); ?> | 
           <strong>Balance:</strong> <?php echo formatCurrency($account['balance']); ?></p>
    </div>
    
    <form method="GET" class="filter-form">
        <input type="hidden" name="acc" value="<?php echo htmlspecialchars($acc); ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="from_date">From Date</label>
                <input type="date" id="from_date" name="from_date" value="<?php echo $fromDate; ?>">
            </div>
            <div class="form-group">
                <label for="to_date">To Date</label>
                <input type="date" id="to_date" name="to_date" value="<?php echo $toDate; ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    
    <?php if (empty($details)): ?>
        <p class="no-data">No transactions found for the selected period.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $detail): ?>
                    <tr>
                        <td><?php echo formatDate($detail['trans_date']); ?></td>
                        <td><?php echo htmlspecialchars($detail['trans_type']); ?></td>
                        <td><?php echo htmlspecialchars($detail['description'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($detail['ref_no'] ?? 'N/A'); ?></td>
                        <td><?php echo formatCurrency($detail['amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="form-actions">
        <a href="bankaccount.php" class="btn btn-secondary">Back</a>
    </div>
</div>
<?php require_once 'footer.php'; ?>
