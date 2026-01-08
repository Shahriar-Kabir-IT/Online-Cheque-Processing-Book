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

// Determine return URL based on referrer or status
$returnUrl = Validation::getGet('return', '');
if (empty($returnUrl)) {
    // Check HTTP referrer
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referrer, 'printed_cheques') !== false) {
        $returnUrl = 'printed_cheques_company.php';
    } elseif (strpos($referrer, 'pending_cheques') !== false) {
        $returnUrl = 'pending_cheques.php';
    } else {
        // Default based on current status
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
    $Cheque->ocq_status = 1; // Printed
    $Cheque->ocq_purpose = $chq['ocq_purpose'];
    $Cheque->ocq_prepare_datetime = $chq['ocq_prepare_datetime'];
    $Cheque->ocq_print_datetime = date('Y-m-d H:i:s');
    $Cheque->Save();
}

// Simple HTML print view (can be enhanced with MPDF later)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cheque Print</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .cheque-print {
            border: 2px solid #000;
            padding: 20px;
            width: 800px;
            margin: 0 auto;
        }
        .cheque-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .cheque-body {
            margin: 20px 0;
        }
        .cheque-line {
            margin: 15px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .amount-words {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print</button>
        <a href="<?php echo htmlspecialchars($returnUrl); ?>" style="padding: 10px 20px; font-size: 16px; margin-left: 10px;">Back</a>
    </div>
    
    <div class="cheque-print">
        <div class="cheque-header">
            <h2>CHEQUE</h2>
        </div>
        
        <div class="cheque-body">
            <div class="cheque-line">
                <strong>Pay:</strong> <?php echo htmlspecialchars($chq['ocq_beneficiary']); ?>
            </div>
            
            <div class="cheque-line">
                <strong>Amount:</strong> <?php echo formatCurrency($chq['ocq_amount']); ?>
            </div>
            
            <div class="cheque-line amount-words">
                <strong>Amount in Words:</strong> <?php echo numberToWords($chq['ocq_amount']); ?> ONLY
            </div>
            
            <div class="cheque-line">
                <strong>Date:</strong> <?php echo formatDate($chq['ocq_date']); ?>
            </div>
            
            <div class="cheque-line">
                <strong>Cheque No:</strong> <?php echo htmlspecialchars($chq['ocq_chqno']); ?>
            </div>
            
            <div class="cheque-line">
                <strong>Account:</strong> <?php echo htmlspecialchars($chq['ocq_accno']); ?>
            </div>
            
            <?php if (!empty($chq['ocq_purpose'])): ?>
                <div class="cheque-line">
                    <strong>Purpose:</strong> <?php echo htmlspecialchars($chq['ocq_purpose']); ?>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 50px; text-align: right;">
                <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; padding-top: 5px;">
                    <strong><?php echo htmlspecialchars($chq['signatory_name'] ?? 'Signature'); ?></strong><br>
                    <?php echo htmlspecialchars($chq['signatory_designation'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
function numberToWords($number) {
    // Number to words conversion
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 
             'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    // Helper function to convert number less than 1000
    function convertHundreds($num, $ones, $tens) {
        $result = '';
        
        if ($num >= 100) {
            $hundreds = floor($num / 100);
            if ($hundreds > 0 && $hundreds < 10) {
                $result .= $ones[$hundreds] . ' Hundred ';
            }
            $num %= 100;
        }
        
        if ($num >= 20) {
            $tensPlace = floor($num / 10);
            if ($tensPlace > 0 && $tensPlace < 10) {
                $result .= $tens[$tensPlace] . ' ';
            }
            $num %= 10;
        }
        
        if ($num > 0 && $num < 20) {
            $result .= $ones[$num] . ' ';
        }
        
        return $result;
    }
    
    $whole = floor($number);
    $fraction = round(($number - $whole) * 100);
    
    if ($whole == 0) {
        return 'Zero' . ($fraction > 0 ? ' and ' . $fraction . '/100' : '');
    }
    
    $result = '';
    
    // Handle Lakhs (100,000)
    if ($whole >= 100000) {
        $lakhs = floor($whole / 100000);
        $result .= convertHundreds($lakhs, $ones, $tens) . 'Lakh ';
        $whole %= 100000;
    }
    
    // Handle Thousands
    if ($whole >= 1000) {
        $thousands = floor($whole / 1000);
        $result .= convertHundreds($thousands, $ones, $tens) . 'Thousand ';
        $whole %= 1000;
    }
    
    // Handle remaining hundreds, tens, and ones
    $result .= convertHundreds($whole, $ones, $tens);
    
    // Handle fraction (paise)
    if ($fraction > 0) {
        $result .= 'and ' . $fraction . '/100';
    }
    
    return trim($result);
}
?>
