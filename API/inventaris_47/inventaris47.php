<?php

require('./API/setting/setting.php');

class inventaris47
{
    private $db;
    private $table = 'inventaris_47';
    private $table_dtawal = 'v1_data_awal_inventaris_47';
    private $setting;

    public function __construct($db)
    {
        $this->setting = new Setting($db);
        $this->db = $db;
    }

    private function getKibNya($idbi, $f1, $f2, $f)
    {
        $table = mappingTableKib($f1, $f2, $f);
        if($table){
            return getDetailsKib($table, $idbi);
        }
        return null;
    }

    private function cariDataAwal($nibar)
    {
        $query = "SELECT idbi,nibar,concat(c1,'.',c,'.',d,'.',e,'.',e1) as kd_skpd,nm_skpd,f1,f2,f,concat(f1,'.',f2,'.',f,'.',g,'.',h,'.',i,'.',j) as kd_barang,nm_barang,noreg,thn_perolehan,jml_barang,asal_usul,kondisi,staset,nilai_buku,tgl_buku_awal FROM " .$this->table_dtawal. " WHERE nibar = :nibar";
        $sqlDataAwal = $this->db->query($query, array("nibar" => $nibar))->getRowArray();

        if($sqlDataAwal){
            $kib = $this->getKibNya($nibar, $sqlDataAwal['f1'], $sqlDataAwal['f2'], $sqlDataAwal['f']);
            if($kib){
                return array_merge($sqlDataAwal, $kib);
            }
            return false;
        }else{
            return false;
        }
    }

    private function dataInventaris()
    {
        $data = array(
            "keberadan_barang" => "ADA", //ADA,TIDAK ADA
            "kode_barang" => "1.3.1.01.01.01.001",
            "kode_barang_status" => "Sesuai", //Sesuai,Tidak Sesuai
            "kode_barang_sensus" => "",
            "nama_barang" => "Tanah sekolah pendidikan",
            "nama_barang_status" => "Sesuai", //Sesuai,Tidak Sesuai
            "nama_barang_sensus" => "",
            "noreg" => "00001",
            "noreg_status" => "Sesuai", //Sesuai,Tidak Sesuai
            "noreg_sensus" => "",
            "kondisi_barang" => 1,
            "kondisi_barang_sensus" => 1, //1,2,3
            "tercatat_ganda_status" => "TIDAK", //YA, TIDAK
            "id_tercatat_ganda" => "",
            "kode_register_ganda" => "",
            "kode_barang_ganda" => "",
            "nama_barang_ganda" => "",
            "spesifikasi_nama_barang_ganda" => "",
            "jumlah_barang_ganda" => "",
            "satuan_barang_ganda" => "",
            "nilai_perolehan_ganda" => "",
            "tanggal_perolehan_ganda" => "",
            "pengguna_barang_ganda" =>"",
            "nilai_perolehan_atribusi_status" => "TIDAK", //YA, TIDAK
            "atribusi_data_awal" => "", //YA, TIDAK
            "id_nibar_atribusi" => "",
            "kode_barang_atribusi" => "",
            "kode_lokasi_atribusi" => "",
            "kode_register_atribusi" => "",
            "nama_barang_atribusi" => "",
            "spesifikasi_nama_barang_atribusi" => ""
        );
        return $data;
    }

    public function newInventaris($nibar)
    {
        $cariDataAwal = $this->cariDataAwal($nibar);
        $dtInventaris = $this->dataInventaris();
        if($cariDataAwal){
            $res = array_merge($cariDataAwal, $dtInventaris);
            $data = array(
                "nibar" => $res["nibar"],
                "idbi" => $res["idbi"]
            );
            return $data;
        }else{
            return false;
        }
    }

}