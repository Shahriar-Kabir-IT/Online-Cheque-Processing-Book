<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/common/Validation.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);

$status = Validation::getGet('status', '');
$fromDate = Validation::getGet('from_date', '');
$toDate = Validation::getGet('to_date', '');

$filter = [];
$params = [];

if (!empty($status)) {
    $filter[] = "c.ocq_status = ?";
    $params[] = $status;
}

if (!empty($fromDate)) {
    $filter[] = "c.ocq_date >= ?";
    $params[] = $fromDate;
}

if (!empty($toDate)) {
    $filter[] = "c.ocq_date <= ?";
    $params[] = $toDate;
}

$filterStr = !empty($filter) ? implode(' AND ', $filter) : '';
$cheques = $Cheque->ListAllCHQ($filterStr, $params, "c.ocq_date", "DESC");

// Simple HTML report (can be enhanced with MPDF)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cheque Report - PDF</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print</button>
        <a href="report.php" style="padding: 10px 20px; font-size: 16px; margin-left: 10px;">Back</a>
    </div>
    
    <h1>Cheque Report</h1>
    <p><strong>Generated:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
    <?php if (!empty($fromDate) || !empty($toDate)): ?>
        <p><strong>Date Range:</strong> <?php echo formatDate($fromDate); ?> to <?php echo formatDate($toDate); ?></p>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>Cheque No</th>
                <th>Company</th>
                <th>Bank</th>
                <th>Account</th>
                <th>Beneficiary</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cheques)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No cheques found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cheques as $chq): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($chq['ocq_chqno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['company_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['bank_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_accno']); ?></td>
                        <td><?php echo htmlspecialchars($chq['ocq_beneficiary']); ?></td>
                        <td><?php echo formatCurrency($chq['ocq_amount']); ?></td>
                        <td><?php echo formatDate($chq['ocq_date']); ?></td>
                        <td>
                            <?php
                            $statusText = ['', 'Printed', 'Pending', 'Cancelled'];
                            echo $statusText[$chq['ocq_status']] ?? 'N/A';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
