<?php
/*
* handle response ke database
*/

require('./API/setting/setting.php');

class dataAwal
{
    private $db;
    private $table = 'data_awal_inventaris_47';
    private $table_skpd = 'ref_skpd';
    private $table_barang = 'ref_barang';
    private $setting;

    public function __construct($db)
    {
        $this->setting = new Setting($db);
        $this->db = $db;
    }

    public function getStatusAsset()
    {
        $Id = 'STATUS_ASET';
        $st = $this->setting->getIdSetting($Id);
        return $st;
    }

    public function getDataAwal()
    {
        $Id = 'DATA_AWAL';
        $st = $this->setting->getIdSetting($Id);
        return $st['nilai'];
    }

    public function pecahF2()
    {
        $dt = $this->getDataAwal();
        $part = explode(";", $dt);
        $unique_numbers = array();
        foreach($part as $val)
        {
            $numbers = explode('.', $val);
             if (isset($numbers[1]) && !in_array($numbers[1], $unique_numbers)) {
                // Menambahkan angka kedua ke array jika belum ada
                $unique_numbers[] = $numbers[1];
            }
        }
        return $unique_numbers;
    }

    public function pecahF()
    {
        $dt = $this->getDataAwal();
        $part = explode(";", $dt);
        $unique_numbers = array();
        foreach($part as $val)
        {
            $numbers = explode('.', $val);
             if (isset($numbers[2]) && !in_array($numbers[2], $unique_numbers)) {
                // Menambahkan angka kedua ke array jika belum ada
                $unique_numbers[] = $numbers[2];
            }
        }
        return $unique_numbers;
    }

    public function getAllData($type='fetch',$wheres = false, $wheresIn = false, $likes = false, $page = 0, $limit = 10)
    {
        $offset = $page * $limit;
        $whereSetting = array('a.status_barang' => array('!=', '3')); //untuk handle data settingan
        $data = array();
        $conditions = array();

        $query = "SELECT a.id as idbi,a.idawal as nibar,concat(a.c1,'.',a.c,'.',a.d,'.',a.e,'.',a.e1) as kd_skpd,b.nm_skpd as nm_skpd,concat(a.f1,'.',a.f2,'.',a.f,'.',a.g,'.',a.h,'.',a.i,'.',a.j) as kd_barang, c.nm_barang ,a.noreg,a.thn_perolehan,a.jml_barang,a.asal_usul,a.kondisi,a.staset,a.nilai_buku,a.tgl_buku_awal FROM " .$this->table . " as a LEFT JOIN " . $this->table_skpd . " as b ON a.c1 = b.c1 AND a.c = b.c AND a.d = b.d AND a.e = b.e AND a.e1 = b.e1 LEFT JOIN " .$this->table_barang. " as c ON a.f1 = c.f1 AND a.f2 = c.f2 AND a.f = c.f AND a.g = c.g AND a.h = c.h AND a.i = c.i AND a.j = c.j";

        if(count($whereSetting) > 0){
            $arrWhereSetting = $this->db->wheresWithOperator($whereSetting);
            $conditions[] = $arrWhereSetting;
            foreach ($whereSetting as $key => $value) {
                $data[] = $value[1];
            }
        }

        if($wheresIn){
            $arrWhereIn = $this->db->wheresIn($wheresIn);
            $conditions[] = $arrWhereIn['query']; // Add WHERE IN clause to conditions array
            $data = array_merge($data, $arrWhereIn['values']); // Merge with existing data
        }

        if($wheres){
            $arrWheres = $this->db->wheres($wheres);
            $conditions[] = $arrWheres; // Add LIKE clause to conditions array
            foreach ($wheres as $key => $value) {
                $data[] = $value;
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        if($type == 'fetch'){
            $query .= " LIMIT " .$offset. ',' .$limit;
            $result = $this->db->query($query, $data)->getResultArray();
        }else{
            $result = $this->db->query($query, $data)->rowCount();
        }
        return $result;
    }

//batas
}