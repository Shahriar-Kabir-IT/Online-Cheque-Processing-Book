<?php
require_once __DIR__ . '/../common/Validation.php';

class ChequeModel
{
    public $ocq_id;
    public $ocq_bank;
    public $ocq_accno;
    public $ocq_chqno;
    public $ocq_company;
    public $ocq_onbehalf;
    public $ocq_signatory;
    public $ocq_beneficiary;
    public $ocq_amount;
    public $ocq_date;
    public $ocq_type;
    public $ocq_status;
    public $ocq_purpose;
    public $ocq_prepare_datetime;
    public $ocq_print_datetime;
    public $ocq_chqbook_datetime;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->ocq_id)) {
            return $this->Update();
        }
        return $this->Insert();
    }

    public function SaveNew()
    {
        return $this->Insert();
    }

    private function Insert()
    {
        $sql = "INSERT INTO ocps_cheque (ocq_bank, ocq_accno, ocq_chqno, ocq_company, ocq_onbehalf, ocq_signatory, 
                ocq_beneficiary, ocq_amount, ocq_date, ocq_type, ocq_status, ocq_purpose, ocq_prepare_datetime) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return $this->dao->Insert($sql, [
            $this->ocq_bank,
            $this->ocq_accno,
            $this->ocq_chqno,
            $this->ocq_company,
            $this->ocq_onbehalf,
            $this->ocq_signatory,
            $this->ocq_beneficiary,
            $this->ocq_amount,
            $this->ocq_date,
            $this->ocq_type,
            $this->ocq_status ?? 2,
            $this->ocq_purpose
        ]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_cheque SET ocq_bank = ?, ocq_accno = ?, ocq_chqno = ?, ocq_company = ?, ocq_onbehalf = ?, 
                ocq_signatory = ?, ocq_beneficiary = ?, ocq_amount = ?, ocq_date = ?, ocq_type = ?, ocq_status = ?, 
                ocq_purpose = ? WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [
            $this->ocq_bank,
            $this->ocq_accno,
            $this->ocq_chqno,
            $this->ocq_company,
            $this->ocq_onbehalf,
            $this->ocq_signatory,
            $this->ocq_beneficiary,
            $this->ocq_amount,
            $this->ocq_date,
            $this->ocq_type,
            $this->ocq_status,
            $this->ocq_purpose,
            $this->ocq_id
        ]);
        return $this->dao->affectedRows($stmt);
    }

    public function CancelMe(): bool
    {
        $sql = "UPDATE ocps_cheque SET ocq_status = 3 WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ocq_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_cheque";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function ListAllCHQ($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT c.*, co.oc_name as company_name, b.ocq_bank as bank_name, 
                s.ocq_signatory as signatory_name, s.ocq_designation as signatory_designation
                FROM ocps_cheque c
                LEFT JOIN ocps_company co ON c.ocq_company = co.oc_id
                LEFT JOIN ocps_bank b ON c.ocq_bank = b.ocq_id
                LEFT JOIN ocps_signatory s ON c.ocq_signatory = s.ocq_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        } else {
            $sql .= " ORDER BY c.ocq_id DESC";
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function ListAllCHQ1($filter, $params, $sortBy, $sortType, $start, $end): array
    {
        $sql = "SELECT c.*, co.oc_name as company_name, b.ocq_bank as bank_name, 
                s.ocq_signatory as signatory_name, s.ocq_designation as signatory_designation
                FROM ocps_cheque c
                LEFT JOIN ocps_company co ON c.ocq_company = co.oc_id
                LEFT JOIN ocps_bank b ON c.ocq_bank = b.ocq_id
                LEFT JOIN ocps_signatory s ON c.ocq_signatory = s.ocq_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        } else {
            $sql .= " ORDER BY c.ocq_id DESC";
        }
        $sql .= " LIMIT " . (int)$start . ", " . (int)$end;
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function getAccSummary($filter, $params, $sortBy, $sortType): array
    {
        $sql = "SELECT ocq_type, COUNT(*) as count, SUM(ocq_amount) as total 
                FROM ocps_cheque";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        $sql .= " GROUP BY ocq_type";
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function getAccSummary1($filter, $params, $sortBy, $sortType): array
    {
        $sql = "SELECT c.ocq_company, co.oc_name, COUNT(*) as count, SUM(c.ocq_amount) as total 
                FROM ocps_cheque c
                LEFT JOIN ocps_company co ON c.ocq_company = co.oc_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        $sql .= " GROUP BY c.ocq_company";
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function getBeneficiaryCHQ(): array
    {
        $sql = "SELECT DISTINCT ocq_beneficiary FROM ocps_cheque WHERE ocq_beneficiary IS NOT NULL AND ocq_beneficiary != ''";
        $stmt = $this->dao->Execute($sql);
        return $this->dao->fetchAll($stmt);
    }

    public function getMaxID(): array
    {
        $sql = "SELECT MAX(ocq_id) as max_ocq_id FROM ocps_cheque";
        $stmt = $this->dao->Execute($sql);
        return $this->dao->fetchArray($stmt);
    }

    public function getCount($filter, $params, $sortBy, $sortType): array
    {
        $sql = "SELECT COUNT(*) as count FROM ocps_cheque c";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchArray($stmt);
    }
}
