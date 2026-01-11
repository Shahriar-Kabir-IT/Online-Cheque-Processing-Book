<?php
$pageTitle = 'Cancelled Cheques';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/common/functions.php';
require_once '../class/common/Validation.php';

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);

// Pagination
$page = Validation::getGet('page', 1, 'int');
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Date range filter
$fromDate = Validation::getGet('from_date', '');
$toDate = Validation::getGet('to_date', '');

// Build filter
$filter = ["c.ocq_status = 3"];
$params = [];

if (!empty($fromDate)) {
    $filter[] = "c.ocq_date >= ?";
    $params[] = $fromDate;
}

if (!empty($toDate)) {
    $filter[] = "c.ocq_date <= ?";
    $params[] = $toDate;
}

$filterStr = implode(' AND ', $filter);

// Get total count for pagination
$countResult = $Cheque->getCount($filterStr, $params, '', '');
$totalRecords = $countResult['count'] ?? 0;
$totalPages = ceil($totalRecords / $perPage);

// Get paginated results
$cancelledCheques = $Cheque->ListAllCHQ1($filterStr, $params, "c.ocq_prepare_datetime", "DESC", $offset, $perPage);
?>
<div class="page-content">
    <h2>Cancelled Cheques</h2>
    
    <!-- Filter Form -->
    <div class="filter-section">
        <form method="GET" action="" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
                </div>
                <div class="form-group">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="cancelled_cheques.php" class="btn btn-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Results Info -->
    <div class="results-info">
        <p>
            Showing <?php echo $offset + 1; ?> - <?php echo min($offset + $perPage, $totalRecords); ?> 
            of <?php echo number_format($totalRecords); ?> cancelled cheques
            <?php if (!empty($fromDate) || !empty($toDate)): ?>
                (filtered)
            <?php endif; ?>
        </p>
    </div>
    
    <?php if (empty($cancelledCheques)): ?>
        <p class="no-data">No cancelled cheques found<?php echo (!empty($fromDate) || !empty($toDate)) ? ' for the selected date range' : ''; ?>.</p>
    <?php else: ?>
        <div class="table-container">
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
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($fromDate) ? '&from_date=' . urlencode($fromDate) : ''; ?><?php echo !empty($toDate) ? '&to_date=' . urlencode($toDate) : ''; ?>" class="btn btn-sm btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <span class="page-info">
                    Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                </span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($fromDate) ? '&from_date=' . urlencode($fromDate) : ''; ?><?php echo !empty($toDate) ? '&to_date=' . urlencode($toDate) : ''; ?>" class="btn btn-sm btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
