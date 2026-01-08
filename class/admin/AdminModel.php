<?php
require_once __DIR__ . '/../common/Validation.php';

class AdminModel
{
    public $oa_id;
    public $oa_login;
    public $oa_password;
    public $oa_name;
    public $oa_department;
    public $oa_last_login;
    public $oa_active;

    private $dao;

    public function __construct(DataAccess $dao)
    {
        $this->dao = $dao;
    }

    public function Save()
    {
        if (!empty($this->oa_id)) {
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
        $sql = "INSERT INTO ocps_admin (oa_login, oa_password, oa_name, oa_department, oa_last_login, oa_active) 
                VALUES (?, ?, ?, ?, NOW(), ?)";
        $password = $this->hashPasswordIfNeeded($this->oa_password);
        return $this->dao->Insert($sql, [
            $this->oa_login,
            $password,
            $this->oa_name,
            $this->oa_department,
            $this->oa_active ?? 1
        ]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_admin SET oa_login = ?, oa_password = ?, oa_name = ?, oa_department = ?, oa_active = ? 
                WHERE oa_id = ?";
        $password = $this->hashPasswordIfNeeded($this->oa_password);
        $stmt = $this->dao->Execute($sql, [
            $this->oa_login,
            $password,
            $this->oa_name,
            $this->oa_department,
            $this->oa_active ?? 1,
            $this->oa_id
        ]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_admin WHERE oa_id = ?";
        $stmt = $this->dao->Execute($sql, [$this->oa_id]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_admin";
        if (!empty($filter)) {
            $sql .= " WHERE " . $filter;
        }
        if (!empty($sortBy)) {
            $sql .= " ORDER BY " . $sortBy . " " . $sortType;
        }
        $stmt = $this->dao->Execute($sql, $params);
        return $this->dao->fetchAll($stmt);
    }

    private function hashPasswordIfNeeded($password): string
    {
        if (empty($password)) {
            return '';
        }
        // If already hashed (starts with $2y$ or similar), return as is
        if (preg_match('/^\$2[ayb]\$.{56}$/', $password)) {
            return $password;
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
