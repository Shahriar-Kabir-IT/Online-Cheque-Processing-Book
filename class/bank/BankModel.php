<?php
require_once __DIR__ . '/../common/Validation.php';

class BankModel
{
    public $ocq_id;
    public $ocq_bank;

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
        $sql = "INSERT INTO ocps_bank (ocq_bank) VALUES (?)";
        return $this->dao->Insert($sql, [$this->ocq_bank]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_bank SET ocq_bank = ? WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ocq_bank, $this->ocq_id]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_bank WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ocq_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_bank";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    public function getMaxID(): array
    {
        $sql = "SELECT MAX(ocq_id) as max_ocq_id FROM ocps_bank";
        $stmt = $this->dao->Execute($sql);
        return $this->dao->fetchArray($stmt);
    }
}
