<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/common/Validation.php';
require_once '../class/account/AccountModel.php';

$dao = new DataAccess();

if (isset($_POST['BtnSaveAccount'])) {
    $Account = new AccountModel($dao);
    $Account->ac_code = Validation::getPost('ac_code');
    $Account->ocq_company = Validation::getPost('ocq_company', 0, 'int');
    $Account->bank = Validation::getPost('bank');
    $Account->branch = Validation::getPost('branch');
    $Account->ac_type = Validation::getPost('ac_type');
    $Account->acc_number = Validation::getPost('acc_number');
    $Account->chequebook = Validation::getPost('chequebook');
    $Account->leafs = Validation::getPost('leafs', 0, 'int');
    $Account->balance = Validation::getPost('balance', 0, 'float');
    $Account->chqbookdate = Validation::getPost('chqbookdate');
    
    $Account->SaveNew();
    header('Location: new_account.php?success=1');
    exit();
}

header('Location: index.php');
exit();
