<?php
require_once __DIR__ . '/../common/Validation.php';

class AccountModel
{
    public $sl;
    public $ac_code;
    public $ocq_company;
    public $bank;
    public $branch;
    public $ac_type;
    public $acc_number;
    public $chequebook;
    public $leafs;
    public $balance;
    public $entrydate;
    public $chqbookdate;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->sl)) {
            return $this->Update();
        }
        return $this->Insert();
    }

    private function Insert()
    {
        $sql = "INSERT INTO ocps_account (ac_code, ocq_company, bank, branch, ac_type, acc_number, chequebook, leafs, balance, entrydate, chqbookdate) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
        return $this->dao->Insert($sql, [
            $this->ac_code,
            $this->ocq_company,
            $this->bank,
            $this->branch,
            $this->ac_type,
            $this->acc_number,
            $this->chequebook,
            $this->leafs,
            $this->balance ?? 0,
            $this->chqbookdate
        ]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_account SET ac_code = ?, ocq_company = ?, bank = ?, branch = ?, ac_type = ?, 
                acc_number = ?, chequebook = ?, leafs = ?, balance = ?, chqbookdate = ? WHERE sl = ?";
        $stmt = $this->dao->Execute($sql, [
            $this->ac_code,
            $this->ocq_company,
            $this->bank,
            $this->branch,
            $this->ac_type,
            $this->acc_number,
            $this->chequebook,
            $this->leafs,
            $this->balance,
            $this->chqbookdate,
            $this->sl
        ]);
        return $this->dao->affectedRows($stmt);
    }

    public function UpdateChequebook(): bool
    {
        $sql = "UPDATE ocps_account SET chequebook = ?, leafs = ?, chqbookdate = ? WHERE acc_number = ?";
        $stmt = $this->dao->Execute($sql, [
            $this->chequebook,
            $this->leafs,
            $this->chqbookdate,
            $this->acc_number
        ]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_account";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function ListAllAccount($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT a.*, co.oc_name as company_name, b.ocq_bank as bank_name 
                FROM ocps_account a
                LEFT JOIN ocps_company co ON a.ocq_company = co.oc_id
                LEFT JOIN ocps_bank b ON a.bank = b.ocq_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function ListAllAccount1($filter, $params, $sortBy, $sortType, $start, $end): array
    {
        $sql = "SELECT a.*, co.oc_name as company_name, b.ocq_bank as bank_name 
                FROM ocps_account a
                LEFT JOIN ocps_company co ON a.ocq_company = co.oc_id
                LEFT JOIN ocps_bank b ON a.bank = b.ocq_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $sql .= " LIMIT " . (int)$start . ", " . (int)$end;
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function getchqbalance($acc, $from, $to)
    {
        $sql = "SELECT SUM(ocq_amount) as total FROM ocps_cheque 
                WHERE ocq_status = 1 AND ocq_accno = ? AND ocq_date BETWEEN ? AND ?";
        $stmt = $this->dao->Execute($sql, [$acc, $from, $to]);
        $result = $this->dao->fetchArray($stmt);
        return $result['total'] ?? 0;
    }

    public function getaccdetails($acc, $from, $to): array
    {
        $sql = "(SELECT ocq_date as trans_date, 'Cheque' as trans_type, ocq_amount as amount, ocq_beneficiary as description, ocq_chqno as ref_no
                 FROM ocps_cheque WHERE ocq_status = 1 AND ocq_accno = ? AND ocq_date BETWEEN ? AND ?)
                UNION ALL
                (SELECT adjustment_date as trans_date, CONCAT('Adjustment (', adjustment_type, ')') as trans_type, 
                 adjustment_amount as amount, adjustment_reason as description, '' as ref_no
                 FROM ocps_adjustment WHERE adjustment_account = ? AND adjustment_date BETWEEN ? AND ?)
                ORDER BY trans_date DESC";
        $stmt = $this->dao->Execute($sql, [$acc, $from, $to, $acc, $from, $to]);
        return $this->dao->fetchAll($stmt);
    }

    public function getadjbalance($acc, $from, $to)
    {
        $sql = "SELECT 
                SUM(CASE WHEN adjustment_type = 'Positive' THEN adjustment_amount ELSE 0 END) as positive,
                SUM(CASE WHEN adjustment_type = 'Negative' THEN adjustment_amount ELSE 0 END) as negative
                FROM ocps_adjustment 
                WHERE adjustment_account = ? AND adjustment_date BETWEEN ? AND ?";
        $stmt = $this->dao->Execute($sql, [$acc, $from, $to]);
        $result = $this->dao->fetchArray($stmt);
        return ($result['positive'] ?? 0) - ($result['negative'] ?? 0);
    }
}
