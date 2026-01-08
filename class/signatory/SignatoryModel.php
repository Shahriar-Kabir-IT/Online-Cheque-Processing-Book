<?php
require_once __DIR__ . '/../common/Validation.php';

class SignatoryModel
{
    public $ocq_id;
    public $ocq_signatory;
    public $ocq_designation;

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
        $sql = "INSERT INTO ocps_signatory (ocq_signatory, ocq_designation) VALUES (?, ?)";
        return $this->dao->Insert($sql, [$this->ocq_signatory, $this->ocq_designation]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_signatory SET ocq_signatory = ?, ocq_designation = ? WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ocq_signatory, $this->ocq_designation, $this->ocq_id]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_signatory WHERE ocq_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->ocq_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_signatory";
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
