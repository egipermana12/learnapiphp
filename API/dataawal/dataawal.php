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

    private function mappingTableKib($f1, $f2, $f)
    {
        if($f1 == '1' && $f2 == '3' && $f == '1'){
            return 'kib_a';
        }else if($f1 == '1' && $f2 == '3' && $f == '2'){
            return 'kib_b';
        }else if($f1 == '1' && $f2 == '3' && $f == '3'){
            return 'kib_c';
        }else if($f1 == '1' && $f2 == '3' && $f == '4'){
            return 'kib_d';
        }else if($f1 == '1' && $f2 == '3' && $f == '5'){
            return 'kib_e';
        }else if($f1 == '1' && $f2 == '3' && $f == '6'){
            return 'kib_f';
        }else if($f1 == '1' && $f2 == '5' && $f == '3'){
            return 'kib_g';
        }
        return;
    }

    private function getDetailsKib($table, $idbi)
    {
        switch ($table) {
            case 'kib_a':
                $query = "SELECT luas, alamat, alamat_kel, alamat_a, alamat_b, alamat_c, alamat_d, penggunaan, ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();

                $data = array();
                $kota = $res['alamat_b'];
                $kec = $res['alamat_c'];
                $desa = $res['alamat_d'];

                if(!empty($kota)){
                    $kotamapp = getKota($kota);
                    $data['kota'] = $kotamapp['nm_wilayah'];
                }else{
                     $data['kota'] = '';
                }
                if(!empty($kec)){
                    $kotamapp = getKec($kota, $kec);
                    $data['kecamatan'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kecamatan'] = '';
                }
                if(!empty($desa)){
                    $kotamapp = getKel($kota, $kec, $desa);
                    $data['desa'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['desa'] = '';
                }

                return array_merge($res, $data);
                break;
            case 'kib_b':
                $query = "SELECT merk,ukuran,bahan,no_pabrik,no_rangka,no_mesin,no_polisi,no_bpkb,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();
                return $res;
                break;
            case 'kib_c':
                $query = "SELECT luas_lantai,alamat,alamat_a,alamat_b,alamat_c,alamat_d,kota,alamat_kel,alamat_kec,luas,status_tanah,kode_tanah,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();

                $data = array();
                $kota = $res['alamat_b'];
                $kec = $res['alamat_c'];
                $desa = $res['alamat_d'];

                if(!empty($kota)){
                    $kotamapp = getKota($kota);
                    $data['kota'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kota'] = '';
                }
                if(!empty($kec)){
                    $kotamapp = getKec($kota, $kec);
                    $data['kecamatan'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kecamatan'] = '';
                }
                if(!empty($desa)){
                    $kotamapp = getKel($kota, $kec, $desa);
                    $data['desa'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['desa'] = '';
                }

                return array_merge($res, $data);
                break;
            case 'kib_d':
                $query = "SELECT panjang,lebar,luas,alamat,alamat_a,alamat_b,alamat_c,alamat_d,kota,alamat_kel,alamat_kec,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();
                $data = array();
                $kota = $res['alamat_b'];
                $kec = $res['alamat_c'];
                $desa = $res['alamat_d'];

                if(!empty($kota)){
                    $kotamapp = getKota($kota);
                    $data['kota'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kota'] = '';
                }
                if(!empty($kec)){
                    $kotamapp = getKec($kota, $kec);
                    $data['kecamatan'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kecamatan'] = '';
                }
                if(!empty($desa)){
                    $kotamapp = getKel($kota, $kec, $desa);
                    $data['desa'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['desa'] = '';
                }

                return array_merge($res, $data);
                break;
            case 'kib_e':
                $query = "SELECT buku_judul,buku_spesifikasi,seni_asal_daerah,seni_pencipta,seni_bahan,hewan_jenis,hewan_ukuran,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();
                return $res;
                break;
            case 'kib_f':
                $query = "SELECT luas,alamat,alamat_a,alamat_b,alamat_c,alamat_d,kota,alamat_kel,alamat_kec,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();
                $data = array();
                $kota = $res['alamat_b'];
                $kec = $res['alamat_c'];
                $desa = $res['alamat_d'];

                if(!empty($kota)){
                    $kotamapp = getKota($kota);
                    $data['kota'] = $kotamapp['nm_wilayah'];
                }else{
                     $data['kota'] = '';
                }
                if(!empty($kec)){
                    $kotamapp = getKec($kota, $kec);
                    $data['kecamatan'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['kecamatan'] = '';
                }
                if(!empty($desa)){
                    $kotamapp = getKel($kota, $kec, $desa);
                    $data['desa'] = $kotamapp['nm_wilayah'];
                }else{
                    $data['desa'] = '';
                }

                return array_merge($res, $data);
                break;
            case 'kib_g':
                $query = "SELECT uraian,software_nama,kajian_nama,kerjasama_nama,ket FROM " .$table . " WHERE idbi = :idbi";
                $res = $this->db->query($query, array('idbi' => $idbi))->getRowArray();
                return $res;
                break;
            default:
                // code...
                break;
        }
    }

    private function getKibNya($idbi, $f1, $f2, $f)
    {
        $table = $this->mappingTableKib($f1, $f2, $f);
        if($table){
            return $this->getDetailsKib($table, $idbi);
        }
        return null;
    }

    public function getKib($type,$wheres, $wheresIn, $likes, $page, $limit)
    {
        $result = $this->getAllData($type,$wheres, $wheresIn, $likes, $page, $limit);
        $data = array();
        foreach($result as $value){
            $getDetailsKib = $this->getKibNya($value['idbi'],$value['f1'],$value['f2'],$value['f']);
            if($getDetailsKib){
                $getDetailsKib = array_map(function($item) {
                    if (is_string($item)) {
                        // Trim whitespace dan hapus \r dan \n
                        $item = trim(preg_replace('/\s+/', ' ', $item));
                    }
                    return $item;
                }, $getDetailsKib);
                $value = array_merge($value, $getDetailsKib);
            }
            $data[] = $value;
        }
        return $data;
    }

//batas
}