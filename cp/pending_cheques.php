<?php
$pageTitle = 'Pending Cheques';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);

$pendingCheques = $Cheque->ListAllCHQ("c.ocq_status = 2", [], "c.ocq_prepare_datetime", "DESC");
?>
<div class="page-content">
    <h2>Pending Cheques</h2>
    
    <?php if (empty($pendingCheques)): ?>
        <p class="no-data">No pending cheques found.</p>
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
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingCheques as $chq): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($chq['ocq_chqno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['company_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['bank_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_accno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_beneficiary']); ?></td>
                        <td><?php echo formatCurrency($chq['ocq_amount']); ?></td>
                        <td><?php echo formatDate($chq['ocq_date']); ?></td>
                        <td><?php echo $chq['ocq_type'] == 1 ? 'Company' : 'Personal'; ?></td>
                        <td>
                            <a href="print_cheque.php?ocq_id=<?php echo $chq['ocq_id']; ?>" class="btn btn-sm btn-primary">Print</a>
                            <a href="edit_cheque.php?ocq_id=<?php echo $chq['ocq_id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="operation_cheque.php?action=delete&ocq_id=<?php echo $chq['ocq_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to cancel this cheque?');">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
