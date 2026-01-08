<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/account/AccountModel.php';
require_once '../class/common/Validation.php';

header('Content-Type: application/json');

$dao = new DataAccess();
$Account = new AccountModel($dao);

$acc = Validation::getGet('acc', '');
$company = Validation::getGet('company', 0, 'int');

if (empty($acc) && $company <= 0) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit();
}

$accounts = [];
if (!empty($acc)) {
    $accounts = $Account->ListAllAccount("a.acc_number = ?", [$acc]);
} elseif ($company > 0) {
    $accounts = $Account->ListAllAccount("a.ocq_company = ?", [$company]);
}

echo json_encode($accounts);
