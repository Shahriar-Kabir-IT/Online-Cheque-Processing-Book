<?php
require_once '../config.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/common/functions.php';
require_once '../class/common/Validation.php';

$ocq_id = Validation::getGet('ocq_id', 0, 'int');
if ($ocq_id <= 0) {
    die('Invalid cheque ID');
}

$dao = new DataAccess();
$Cheque = new ChequeModel($dao);
$cheques = $Cheque->ListAllCHQ("c.ocq_id = ?", [$ocq_id]);

if (empty($cheques)) {
    die('Cheque not found');
}

$chq = $cheques[0];

// Determine return URL
$returnUrl = Validation::getGet('return', '');
if (empty($returnUrl)) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referrer, 'printed_cheques') !== false) {
        $returnUrl = 'printed_cheques_company.php';
    } elseif (strpos($referrer, 'pending_cheques') !== false) {
        $returnUrl = 'pending_cheques.php';
    } else {
        $returnUrl = ($chq['ocq_status'] == 1) ? 'printed_cheques_company.php' : 'pending_cheques.php';
    }
}

// Update status to printed if pending
if ($chq['ocq_status'] == 2) {
    $Cheque->ocq_id = $chq['ocq_id'];
    $Cheque->ocq_bank = $chq['ocq_bank'];
    $Cheque->ocq_accno = $chq['ocq_accno'];
    $Cheque->ocq_chqno = $chq['ocq_chqno'];
    $Cheque->ocq_company = $chq['ocq_company'];
    $Cheque->ocq_onbehalf = $chq['ocq_onbehalf'];
    $Cheque->ocq_signatory = $chq['ocq_signatory'];
    $Cheque->ocq_beneficiary = $chq['ocq_beneficiary'];
    $Cheque->ocq_amount = $chq['ocq_amount'];
    $Cheque->ocq_date = $chq['ocq_date'];
    $Cheque->ocq_type = $chq['ocq_type'];
    $Cheque->ocq_status = 1;
    $Cheque->ocq_purpose = $chq['ocq_purpose'];
    $Cheque->ocq_prepare_datetime = $chq['ocq_prepare_datetime'];
    $Cheque->ocq_print_datetime = date('Y-m-d H:i:s');
    $Cheque->Save();
}

// Process amount exactly like original preview_cheque.php
$amount = $chq['ocq_amount'];
$amount_in_words = addAnd(no_to_words($amount)) . " Only";

$lines_amount_in_words = explode("\n", wordwrap($amount_in_words, 47, "\n", true));
$total_lines_amount_in_words = count($lines_amount_in_words);

// Date formatting - exactly like original
$dateStr = MySqlDateToPhpDate($chq['ocq_date']); // Returns DDMMYYYY (no slashes)

// Company and signatory
$company = strtoupper($chq['company_name'] ?? '');
$signatory = strtoupper($chq['signatory_name'] ?? '');
$designation = $chq['signatory_designation'] ?? '';
$payTo = $chq['ocq_beneficiary'];
$ocq_type = $chq['ocq_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cheque Print</title>
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 0;
            }
            .no-print { 
                display: none; 
            }
            @page {
                size: landscape;
                margin: 0;
            }
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .cheque-wrapper {
            width: 550px;
            margin: 0 auto;
        }
        
        table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            border-spacing: 0;
        }
        
        td {
            margin: 0;
            padding: 0;
            vertical-align: top;
        }
        
        h1 {
            font-family: times;
            font-size: 20pt;
            margin: 0;
            padding: 0;
        }
        
        h2 {
            font-family: times;
            font-size: 14pt;
            margin: 0;
            padding: 0;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .no-print button,
        .no-print a {
            padding: 10px 20px;
            font-size: 16px;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
            background: #3f7297;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .no-print a {
            background: #6b7280;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="<?php echo htmlspecialchars($returnUrl); ?>">Back</a>
    </div>
    
    <div class="cheque-wrapper">
        <!-- Exact HTML structure from original preview_cheque.php -->
        <table>
            <tr>
                <td rowspan="4">
                    <?php if ($ocq_type == 1): ?>
                        <div style="width: 50px; height: 60px; border: 2px solid #000; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 7px; font-weight: bold; text-align: center; line-height: 1.1; padding: 2px; font-family: Arial, sans-serif;">
                            A/C<br>PAYEE<br>ONLY
                        </div>
                    <?php else: ?>
                        <div style="width: 50px; height: 60px; border: 2px solid #000; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 7px; font-weight: bold; text-align: center; line-height: 1.1; padding: 2px; font-family: Arial, sans-serif;">
                            PERSONAL
                        </div>
                    <?php endif; ?>
                </td>
                <td width="350px"></td>
                <td></td>
            </tr>
            <tr>
                <td width="350px"></td>
                <td height="30px"></td>
            </tr>
            <tr>
                <td width="252px"></td>
                <td height="40px">
                    <table>
                        <tr>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -8, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -7, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -6, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -5, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -4, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -3, 1); ?></h2></td>
                            <td height="35px" width="24px"><h2><?php echo substr($dateStr, -2, 1); ?></h2></td>
                            <td height="35px" width="22px"><h2><?php echo substr($dateStr, -1, 1); ?></h2></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <br />
        
        <table>
            <tr>
                <td width="52px" height="22px"></td>
                <td width="525px" height="32px"><?php echo htmlspecialchars($payTo); ?></td>
            </tr>
        </table>
        
        <table>
            <tr>
                <td width="105px"></td>
                <td width="330px" height="20px"><?php echo htmlspecialchars($lines_amount_in_words[0] ?? ''); ?> </td>
                <td width="40px"></td>
                <td width="235px" align="left"><h1><?php echo htmlspecialchars(numberFormat($amount)); ?>.00</h1></td>
            </tr>
        </table>
        
        <?php if ($total_lines_amount_in_words != 1): ?>
            <table>
                <tr>
                    <td width="20px"></td>
                    <td width="425px" height="25px"><?php echo htmlspecialchars($lines_amount_in_words[1] ?? ''); ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
