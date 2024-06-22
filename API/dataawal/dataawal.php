<?php
/*
* handle response ke database
*/

require('./API/setting/setting.php');

class dataAwal
{
    private $db;
    private $table = 'v1_data_awal_inventaris_47';
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

    public function getTahunSensus()
    {
        $Id = 'TAHUN_INVENTARIS';
        $st = $this->setting->getIdSetting($Id);
        return $st['nilai'];
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

    public function getStatusSensus($idbi, $tahun_sensus)
    {
        $query = "SELECT idbi FROM inventaris_47 WHERE idbi = :idbi and tahun_sensus = :tahun_sensus ";
        $res = $this->db->query($query, array('idbi' => $idbi, 'tahun_sensus' => $tahun_sensus))->getRowArray();
        return $res;
    }

    public function getAllData($type='fetch',$wheres = false, $wheresIn = false, $likes = false, $page = 0, $limit = 10)
    {
        $offset = $page * $limit;
        $whereSetting = array('a.status_barang' => array('!=', '3')); //untuk handle data settingan
        $data = array();
        $conditions = array();

        $query = "SELECT idbi,nibar,concat(c1,'.',c,'.',d,'.',e,'.',e1) as kd_skpd,nm_skpd,f1,f2,f,concat(f1,'.',f2,'.',f,'.',g,'.',h,'.',i,'.',j) as kd_barang,nm_barang,noreg,thn_perolehan,jml_barang,asal_usul,kondisi,staset,nilai_buku,tgl_buku_awal FROM " .$this->table ;

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

        if($likes){
            $arrLike = $this->db->likes($likes);
            $conditions[] = $arrLike; // Add LIKE clause to conditions array
            foreach ($likes as $key => $value) {
                $data[] = "%".$value."%";
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

    private function getKibNya($idbi, $f1, $f2, $f)
    {
        $table = mappingTableKib($f1, $f2, $f);
        if($table){
            return getDetailsKib($table, $idbi);
        }
        return null;
    }

    public function getKib($type,$wheres, $wheresIn, $likes, $page, $limit)
    {
        $result = $this->getAllData($type,$wheres, $wheresIn, $likes, $page, $limit);
        $data = array();
        foreach($result as $value){

            $getDetailsKib = $this->getKibNya($value['idbi'],$value['f1'],$value['f2'],$value['f']);
            $getStatusSensus = $this->getStatusSensus($value['idbi'], $this->getTahunSensus());
            $stSensus = !empty($getStatusSensus) ? 'SUDAH': 'BELUM' ;

            if($getDetailsKib){
                $getDetailsKib = array_map(function($item) {
                    if (is_string($item)) {
                        // Trim whitespace dan hapus \r dan \n
                        $item = trim(preg_replace('/\s+/', ' ', $item));
                    }
                    return $item;
                }, $getDetailsKib);
                $join = array_merge($getDetailsKib, array('status_sensus' => $stSensus));
                $value = array_merge($value, $join);
            }
            $data[] = $value;
        }
        return $data;
    }

//batas
}