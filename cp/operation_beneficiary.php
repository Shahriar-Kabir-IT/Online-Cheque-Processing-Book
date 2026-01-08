<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/beneficiary/BeneficiaryModel.php';

$dao = new DataAccess();

if (isset($_POST['BtnSaveBeneficiary'])) {
    $Beneficiary = new BeneficiaryModel($dao);
    $Beneficiary->ob_name = Validation::getPost('ob_name');
    
    // Check if exists
    $existing = $Beneficiary->ListAll("ob_name = ?", [$Beneficiary->ob_name]);
    if (empty($existing)) {
        $Beneficiary->SaveNew();
    }
    header('Location: manage_beneficiary.php?success=1');
    exit();
}

if (isset($_POST['BtnUpdateBeneficiary'])) {
    $Beneficiary = new BeneficiaryModel($dao);
    $Beneficiary->ob_id = Validation::getPost('ob_id', 0, 'int');
    $Beneficiary->ob_name = Validation::getPost('ob_name');
    
    if ($Beneficiary->ob_id > 0) {
        $Beneficiary->Save();
    }
    header('Location: manage_beneficiary.php?success=1');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $ob_id = Validation::getGet('ob_id', 0, 'int');
    if ($ob_id > 0) {
        $Beneficiary = new BeneficiaryModel($dao);
        $Beneficiary->ob_id = $ob_id;
        $Beneficiary->DeleteMe();
    }
    header('Location: manage_beneficiary.php');
    exit();
}

header('Location: manage_beneficiary.php');
exit();
