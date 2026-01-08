<?php
require_once __DIR__ . '/../common/Validation.php';

class CompanyModel
{
    public $oc_id;
    public $oc_name;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->oc_id)) {
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
        $sql = "INSERT INTO ocps_company (oc_name) VALUES (?)";
        return $this->dao->Insert($sql, [$this->oc_name]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_company SET oc_name = ? WHERE oc_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->oc_name, $this->oc_id]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_company WHERE oc_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->oc_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_company";
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
