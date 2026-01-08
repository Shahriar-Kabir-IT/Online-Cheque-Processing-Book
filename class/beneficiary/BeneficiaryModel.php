<?php
require_once __DIR__ . '/../common/Validation.php';

class BeneficiaryModel
{
    public $ob_id;
    public $ob_name;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->ob_id)) {
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
        $sql = "INSERT INTO ocps_beneficiary (ob_name) VALUES (?)";
        return $this->dao->Insert($sql, [$this->ob_name]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_beneficiary SET ob_name = ? WHERE ob_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ob_name, $this->ob_id]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_beneficiary WHERE ob_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ob_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_beneficiary";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function ListAll1($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT DISTINCT ocq_beneficiary as ob_name FROM ocps_cheque WHERE ocq_beneficiary IS NOT NULL AND ocq_beneficiary != ''";
        if (!empty($filter)) {
            $sql .= " AND " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }
}
