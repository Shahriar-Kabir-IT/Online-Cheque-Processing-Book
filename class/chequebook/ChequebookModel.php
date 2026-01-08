<?php
require_once __DIR__ . '/../common/Validation.php';

class ChequebookModel
{
    public $sl;
    public $oc_id;
    public $bank;
    public $account;
    public $chqbook_number;
    public $leafs;
    public $inuse;
    public $entrydate;

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

    public function SaveNew()
    {
        return $this->Insert();
    }

    private function Insert()
    {
        $sql = "INSERT INTO ocps_chequebook (oc_id, bank, account, chqbook_number, leafs, inuse, entrydate) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        return $this->dao->Insert($sql, [
            $this->oc_id,
            $this->bank,
            $this->account,
            $this->chqbook_number,
            $this->leafs,
            $this->inuse ?? 0
        ]);
    }

    private function Update()
    {
        $sql = "UPDATE ocps_chequebook SET oc_id = ?, bank = ?, account = ?, chqbook_number = ?, leafs = ?, inuse = ? 
                WHERE sl = ?";
        $stmt = $this->dao->Execute($sql, [
            $this->oc_id,
            $this->bank,
            $this->account,
            $this->chqbook_number,
            $this->leafs,
            $this->inuse,
            $this->sl
        ]);
        return $this->dao->affectedRows($stmt);
    }

    public function DeleteMe(): bool
    {
        $sql = "DELETE FROM ocps_chequebook WHERE sl = ?";
        $stmt = $this->dao->Execute($sql, [$this->sl]);
        return $this->dao->affectedRows($stmt) > 0;
    }

    public function ListAll($filter = '', $params = [], $sortBy = '', $sortType = 'ASC'): array
    {
        $sql = "SELECT * FROM ocps_chequebook";
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
