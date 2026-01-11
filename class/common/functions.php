<?php
/**
 * Helper functions for date conversion and formatting
 */

function PhpDateToMySqlDate($date): string
{
    if (empty($date)) {
        return '';
    }
    // Handle DD/MM/YYYY format
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }
    // Already in YYYY-MM-DD format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return $date;
    }
    return '';
}

function MySqlDateToPhpDate($date): string
{
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    // Convert YYYY-MM-DD to DDMMYYYY (no slashes) - matching original OCPB code
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
        return $matches[3] . $matches[2] . $matches[1]; // DDMMYYYY
    }
    // If already in DD/MM/YYYY format, convert to DDMMYYYY
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return $matches[1] . $matches[2] . $matches[3]; // DDMMYYYY
    }
    return $date;
}

function formatCurrency($amount): string
{
    return number_format($amount, 2, '.', ',');
}

function formatDate($date): string
{
    return MySqlDateToPhpDate($date);
}

// Number to words conversion (from original OCPB)
$words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fourteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Forty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninety','100' => 'Hundred','1000' => 'Thousand','100000' => 'Lac','10000000' => 'Crore');

function no_to_words($no) {
    global $words;
    if($no == 0)
        return ' ';
    else {
        $novalue='';
        $highno=$no;
        $remainno=0;
        $value=100;
        $value1=1000;
        while($no>=100) {
            if(($value <= $no) &&($no  < $value1)) {
                $novalue=$words["$value"];
                $highno = (int)($no/$value);
                $remainno = $no % $value;
                break;
            }
            $value= $value1;
            $value1 = $value * 100;
        }
        if(array_key_exists("$highno",$words))
            return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
        else {
            $unit=$highno%10;
            $ten =(int)($highno/10)*10;
            return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
        }
    }
}

function addAnd($words) {
    $words = explode(" ", $words);
    $count = count($words) - 2;
    $str = '';
    if($count > 3) {
        for($i=0; $i<$count; $i++) {
            if($i == $count-3) {
                if($words[$i] == "Ninety" || $words[$i] == "Eighty" || $words[$i] == "Seventy" || $words[$i] == "Sixty" || $words[$i] == "Fifty" || $words[$i] == "Forty" || $words[$i] == "Thirty" || $words[$i] == "Twenty") {
                    $str = $str." and ".$words[$i];
                } else {
                    for($j=0; $j<$count; $j++) {
                        if($j == $count-2) {
                            if($words[$j] != "Hundred" && $words[$j] != "Thousand" && $words[$j] != "Lakh" && $words[$j] != "Crore") {
                                $str = $str." ".$words[$i]." and ";
                            }
                        }
                    }
                }
            } else {
                $str = $str." ".$words[$i];
            }
        }
    } else {
        for($i=0; $i<$count; $i++) {
            $str = $str." ".$words[$i];
        }
    }
    return trim($str);
}

function numberFormat($val) {
    // Convert to string and remove decimal part if present
    $strPresentSalary = (string)(int)$val;
    $strPresentSalaryLength = strlen($strPresentSalary);
    
    if($strPresentSalaryLength == 0) {
        $strPresentSalary = "0";
    } else if($strPresentSalaryLength < 4) {
        $strPresentSalary = $strPresentSalary;
    } else if($strPresentSalaryLength == 4) {
        $strPresentSalary = $strPresentSalary[0].",".$strPresentSalary[1].$strPresentSalary[2].$strPresentSalary[3];
    } else if($strPresentSalaryLength == 5) {
        $strPresentSalary = $strPresentSalary[0].$strPresentSalary[1].",".$strPresentSalary[2].$strPresentSalary[3].$strPresentSalary[4];
    } else if($strPresentSalaryLength == 6) {
        $strPresentSalary = $strPresentSalary[0].",".$strPresentSalary[1].$strPresentSalary[2].",".$strPresentSalary[3].$strPresentSalary[4].$strPresentSalary[5];
    } else if($strPresentSalaryLength == 7) {
        $strPresentSalary = $strPresentSalary[0].$strPresentSalary[1].",".$strPresentSalary[2].$strPresentSalary[3].",".$strPresentSalary[4].$strPresentSalary[5].$strPresentSalary[6];
    } else if($strPresentSalaryLength == 8) {
        $strPresentSalary = $strPresentSalary[0].",".$strPresentSalary[1].$strPresentSalary[2].",".$strPresentSalary[3].$strPresentSalary[4].",".$strPresentSalary[5].$strPresentSalary[6].$strPresentSalary[7];
    } else if($strPresentSalaryLength == 9) {
        $strPresentSalary = $strPresentSalary[0].$strPresentSalary[1].",".$strPresentSalary[2].$strPresentSalary[3].",".$strPresentSalary[4].$strPresentSalary[5].",".$strPresentSalary[6].$strPresentSalary[7].$strPresentSalary[8];
    }
    
    return $strPresentSalary;
}
