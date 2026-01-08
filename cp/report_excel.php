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

// Output as CSV/Excel
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="cheque_report_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// BOM for Excel UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Headers
fputcsv($output, ['Cheque No', 'Company', 'Bank', 'Account', 'Beneficiary', 'Amount', 'Date', 'Status']);

// Data
foreach ($cheques as $chq) {
    $statusText = ['', 'Printed', 'Pending', 'Cancelled'];
    fputcsv($output, [
        $chq['ocq_chqno'],
        $chq['company_name'] ?? 'N/A',
        $chq['bank_name'] ?? 'N/A',
        $chq['ocq_accno'],
        $chq['ocq_beneficiary'],
        $chq['ocq_amount'],
        formatDate($chq['ocq_date']),
        $statusText[$chq['ocq_status']] ?? 'N/A'
    ]);
}

fclose($output);
exit();
