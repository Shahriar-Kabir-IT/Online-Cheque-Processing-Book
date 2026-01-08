<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/common/functions.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/beneficiary/BeneficiaryModel.php';
require_once '../class/account/AccountModel.php';
require_once '../class/adjustment/AdjustmentModel.php';

$dao = new DataAccess();

// Handle cheque save
if (isset($_POST['BtnSaveCheque'])) {
    $Cheque = new ChequeModel($dao);
    $Cheque->ocq_bank = Validation::getPost('ocq_bank');
    $Cheque->ocq_accno = Validation::getPost('ocq_accno');
    $Cheque->ocq_chqno = Validation::getPost('ocq_chqno');
    $Cheque->ocq_company = Validation::getPost('ocq_company', 0, 'int');
    $Cheque->ocq_onbehalf = Validation::getPost('ocq_onbehalf');
    $Cheque->ocq_signatory = Validation::getPost('ocq_signatory');
    $Cheque->ocq_beneficiary = Validation::getPost('ocq_beneficiary');
    $Cheque->ocq_amount = Validation::getPost('ocq_amount', 0, 'float');
    $Cheque->ocq_date = PhpDateToMySqlDate(Validation::getPost('ocq_date'));
    $Cheque->ocq_type = Validation::getPost('ocq_type', 1, 'int');
    $Cheque->ocq_status = 2; // Pending
    $Cheque->ocq_purpose = Validation::getPost('ocq_purpose');
    
    // Save beneficiary if new
    if (!empty($Cheque->ocq_beneficiary)) {
        $Beneficiary = new BeneficiaryModel($dao);
        $existing = $Beneficiary->ListAll("ob_name = ?", [$Cheque->ocq_beneficiary]);
        if (empty($existing)) {
            $Beneficiary->ob_name = $Cheque->ocq_beneficiary;
            $Beneficiary->SaveNew();
        }
    }
    
    $chequeId = $Cheque->SaveNew();
    header('Location: new_cheque.php?oc_id=' . $Cheque->ocq_company . '&success=1');
    exit();
}

// Handle cheque update
if (isset($_POST['BtnUpdateCheque'])) {
    $Cheque = new ChequeModel($dao);
    $Cheque->ocq_id = Validation::getPost('ocq_id', 0, 'int');
    $Cheque->ocq_bank = Validation::getPost('ocq_bank');
    $Cheque->ocq_accno = Validation::getPost('ocq_accno');
    $Cheque->ocq_chqno = Validation::getPost('ocq_chqno');
    $Cheque->ocq_company = Validation::getPost('ocq_company', 0, 'int');
    $Cheque->ocq_onbehalf = Validation::getPost('ocq_onbehalf');
    $Cheque->ocq_signatory = Validation::getPost('ocq_signatory');
    $Cheque->ocq_beneficiary = Validation::getPost('ocq_beneficiary');
    $Cheque->ocq_amount = Validation::getPost('ocq_amount', 0, 'float');
    $Cheque->ocq_date = PhpDateToMySqlDate(Validation::getPost('ocq_date'));
    $Cheque->ocq_type = Validation::getPost('ocq_type', 1, 'int');
    $Cheque->ocq_status = Validation::getPost('ocq_status', 2, 'int');
    $Cheque->ocq_purpose = Validation::getPost('ocq_purpose');
    
    $Cheque->Save();
    header('Location: edit_cheque.php?ocq_id=' . $Cheque->ocq_id . '&success=1');
    exit();
}

// Handle cheque delete/cancel
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $ocq_id = Validation::getGet('ocq_id', 0, 'int');
    if ($ocq_id > 0) {
        $Cheque = new ChequeModel($dao);
        $Cheque->ocq_id = $ocq_id;
        $Cheque->CancelMe();
    }
    header('Location: pending_cheques.php');
    exit();
}

// Handle adjustment save
if (isset($_POST['BtnSaveAdjustment'])) {
    $Adjustment = new AdjustmentModel($dao);
    $Adjustment->adjustment_bank = Validation::getPost('adjustment_bank', 0, 'int');
    $Adjustment->adjustment_company = Validation::getPost('adjustment_company', 0, 'int');
    $Adjustment->adjustment_type = Validation::getPost('adjustment_type');
    $Adjustment->adjustment_account = Validation::getPost('adjustment_account');
    $Adjustment->adjustment_date = PhpDateToMySqlDate(Validation::getPost('adjustment_date'));
    $Adjustment->adjustment_reason = Validation::getPost('adjustment_reason');
    $Adjustment->adjustment_amount = Validation::getPost('adjustment_amount', 0, 'float');
    
    $Adjustment->SaveNew();
    
    // Update account balance
    $Account = new AccountModel($dao);
    $accounts = $Account->ListAll("acc_number = ?", [$Adjustment->adjustment_account]);
    if (!empty($accounts)) {
        $acc = $accounts[0];
        $Account->sl = $acc['sl'];
        $Account->ac_code = $acc['ac_code'];
        $Account->ocq_company = $acc['ocq_company'];
        $Account->bank = $acc['bank'];
        $Account->branch = $acc['branch'];
        $Account->ac_type = $acc['ac_type'];
        $Account->acc_number = $acc['acc_number'];
        $Account->chequebook = $acc['chequebook'];
        $Account->leafs = $acc['leafs'];
        $Account->balance = $acc['balance'];
        $Account->chqbookdate = $acc['chqbookdate'];
        
        if ($Adjustment->adjustment_type === 'Positive') {
            $Account->balance += $Adjustment->adjustment_amount;
        } else {
            $Account->balance -= $Adjustment->adjustment_amount;
        }
        
        $Account->Save();
    }
    
    header('Location: new_adjustment.php?success=1');
    exit();
}

// Handle report generation
if (isset($_POST['BtnPublishReport'])) {
    $reportType = Validation::getPost('report_type', 1, 'int');
    $status = Validation::getPost('status', '', 'int');
    $fromDate = PhpDateToMySqlDate(Validation::getPost('from_date'));
    $toDate = PhpDateToMySqlDate(Validation::getPost('to_date'));
    
    $params = [];
    $query = [];
    
    if (!empty($status)) {
        $query[] = 'status=' . urlencode($status);
    }
    if (!empty($fromDate)) {
        $query[] = 'from_date=' . urlencode($fromDate);
    }
    if (!empty($toDate)) {
        $query[] = 'to_date=' . urlencode($toDate);
    }
    
    $queryString = !empty($query) ? '?' . implode('&', $query) : '';
    
    if ($reportType == 1) {
        header('Location: report_pdf.php' . $queryString);
    } else {
        header('Location: report_excel.php' . $queryString);
    }
    exit();
}

header('Location: index.php');
exit();
