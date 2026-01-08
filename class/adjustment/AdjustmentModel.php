<?php
require_once __DIR__ . '/../common/Validation.php';

class AdjustmentModel
{
    public $id;
    public $adjustment_bank;
    public $adjustment_company;
    public $adjustment_type;
    public $adjustment_account;
    public $adjustment_date;
    public $adjustment_reason;
    public $adjustment_amount;
    public $entry_date;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->id)) {
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
        $sql = "INSERT INTO ocps_adjustment (adjustment_bank, adjustment_company, adjustment_type, adjustment_account, 
                adjustment_date, adjustment_reason, adjustment_amount, entry_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
        return $this->dao->Insert($sql, [
            $this->adjustment_bank,
            $this->adjustment_company,
            $this->adjustment_type,
            $this->adjustment_account,
            $this->adjustment_date,
            $this->adjustment_reason,
            $this->adjustment_amount
        ]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_adjustment SET adjustment_bank = ?, adjustment_company = ?, adjustment_type = ?, 
                adjustment_account = ?, adjustment_date = ?, adjustment_reason = ?, adjustment_amount = ? 
                WHERE id = ?";
        $stmt = $this->dao->Execute($sql, [
            $this->adjustment_bank,
            $this->adjustment_company,
            $this->adjustment_type,
            $this->adjustment_account,
            $this->adjustment_date,
            $this->adjustment_reason,
            $this->adjustment_amount,
            $this->id
        ]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_adjustment WHERE id = ?";
        $stmt = $this->dao->Execute($sql, [$this->id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT a.*, co.oc_name as company_name, b.ocq_bank as bank_name 
                FROM ocps_adjustment a
                LEFT JOIN ocps_company co ON a.adjustment_company = co.oc_id
                LEFT JOIN ocps_bank b ON a.adjustment_bank = b.ocq_id";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }
}
