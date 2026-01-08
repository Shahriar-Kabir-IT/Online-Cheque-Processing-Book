<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/signatory/SignatoryModel.php';

header('Content-Type: application/json');

$dao = new DataAccess();
$Signatory = new SignatoryModel($dao);

$signatories = $Signatory->ListAll('', [], 'ocq_signatory', 'ASC');

echo json_encode($signatories);
