<?php
$pageTitle = 'Dashboard';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/account/AccountModel.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);
$Account = new AccountModel($dao);

// Get statistics
$pendingCount = count($Cheque->ListAll("ocq_status = 2"));
$printedCount = count($Cheque->ListAll("ocq_status = 1"));
$cancelledCount = count($Cheque->ListAll("ocq_status = 3"));
$totalAccounts = count($Account->ListAll());

// Get recent pending cheques
$recentPending = $Cheque->ListAllCHQ("c.ocq_status = 2", [], "c.ocq_prepare_datetime", "DESC");
$recentPending = array_slice($recentPending, 0, 5);

// Get account summary
$accounts = $Account->ListAllAccount();
?>
<div class="dashboard">
    <h2>Dashboard</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">📋</div>
            <div class="stat-info">
                <h3><?php echo $pendingCount; ?></h3>
                <p>Pending Cheques</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon printed">✅</div>
            <div class="stat-info">
                <h3><?php echo $printedCount; ?></h3>
                <p>Printed Cheques</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon cancelled">❌</div>
            <div class="stat-info">
                <h3><?php echo $cancelledCount; ?></h3>
                <p>Cancelled Cheques</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon accounts">🏦</div>
            <div class="stat-info">
                <h3><?php echo $totalAccounts; ?></h3>
                <p>Bank Accounts</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h3>Recent Pending Cheques</h3>
            <?php if (empty($recentPending)): ?>
                <p class="no-data">No pending cheques</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cheque No</th>
                            <th>Company</th>
                            <th>Beneficiary</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPending as $chq): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($chq['ocq_chqno']); ?></td>
                                <td><?php echo htmlspecialchars($chq['company_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($chq['ocq_beneficiary']); ?></td>
                                <td><?php echo formatCurrency($chq['ocq_amount']); ?></td>
                                <td><?php echo formatDate($chq['ocq_date']); ?></td>
                                <td>
                                    <a href="print_cheque.php?ocq_id=<?php echo $chq['ocq_id']; ?>" class="btn btn-sm btn-primary">Print</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="dashboard-section">
            <h3>Account Summary</h3>
            <?php if (empty($accounts)): ?>
                <p class="no-data">No accounts found</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Account Number</th>
                            <th>Company</th>
                            <th>Bank</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($accounts, 0, 5) as $acc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($acc['acc_number']); ?></td>
                                <td><?php echo htmlspecialchars($acc['company_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($acc['bank_name'] ?? 'N/A'); ?></td>
                                <td><?php echo formatCurrency($acc['balance']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
