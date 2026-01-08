<?php
$pageTitle = 'Cancelled Cheques';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);

$cancelledCheques = $Cheque->ListAllCHQ("c.ocq_status = 3", [], "c.ocq_prepare_datetime", "DESC");
?>
<div class="page-content">
    <h2>Cancelled Cheques</h2>
    
    <?php if (empty($cancelledCheques)): ?>
        <p class="no-data">No cancelled cheques found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cheque No</th>
                    <th>Company</th>
                    <th>Bank</th>
                    <th>Account</th>
                    <th>Beneficiary</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Prepared On</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cancelledCheques as $chq): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($chq['ocq_chqno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['company_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['bank_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_accno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_beneficiary']); ?></td>
                        <td><?php echo formatCurrency($chq['ocq_amount']); ?></td>
                        <td><?php echo formatDate($chq['ocq_date']); ?></td>
                        <td><?php echo $chq['ocq_prepare_datetime'] ? date('d/m/Y H:i', strtotime($chq['ocq_prepare_datetime'])) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
