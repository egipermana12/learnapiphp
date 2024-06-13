<?php

class Setting
{
    private $db;
    private $table = 'setting';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllSetting()
    {
        $sql = "SELECT Id,nilai FROM " .$this->table;
        $query = $this->db->query($sql)->getResultArray();
        $respon = array();
        // foreach ($query as $key => $value) {
        //     $respon[$value['Id']] = $value['nilai'];
        // }
        return $query;
    }

    public function getStatusAset()
    {
        $sql = "SELECT Id,nilai FROM " .$this->table . " WHERE Id= :Id";
        $query = $this->db->query($sql, array('Id' => 'STATUS_ASET'))->getRowArray();
        $respon = array();
    }

    public function getBarangSensus()
    {
        $sql = "SELECT Id,nilai FROM " .$this->table . " WHERE Id= :Id";
        $query = $this->db->query($sql, array('Id' => 'DATA_BARANG'))->getRowArray();
        $respon = array();
    }

}