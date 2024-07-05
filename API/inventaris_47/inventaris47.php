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

    private function mappingRincianInventaris($data = array())
    {
        $f1 = $data["f1"];
        $f2 = $data["f2"];
        $f = $data["f"];
        $res = array("f" => $f1);

        //global
        $res["IdInventaris"] = $data["Id"] ? $data["Id"] : "";
        $res["keberadaan_barang_sensus"] = $data["keberadaan_barang_sensus"] ? $data["keberadaan_barang_sensus"] : "ADA";
        $res["noreg_status"] = $data["noreg_status"] ? $data["noreg_status"] : "Sesuai";
        $res["noreg_sensus"] = $data["noreg_sensus"] ? $data["noreg_sensus"] : "";
        $res["kode_barang_status"] = $data["kode_barang_status"] ? $data["kode_barang_status"] : "Sesuai";
        $res["f1_sensus"] = $data["f1_sensus"] ? $data["f1_sensus"] : "";
        $res["f2_sensus"] = $data["f2_sensus"] ? $data["f2_sensus"] : "";
        $res["f_sensus"] = $data["f_sensus"] ? $data["f_sensus"] : "";
        $res["g_sensus"] = $data["g_sensus"] ? $data["g_sensus"] : "";
        $res["h_sensus"] = $data["h_sensus"] ? $data["h_sensus"] : "";
        $res["i_sensus"] = $data["i_sensus"] ? $data["i_sensus"] : "";
        $res["j_sensus"] = $data["j_sensus"] ? $data["j_sensus"] : "";

        //kib b
        if($f1 == "1" && $f2 =="3" && $f == "2"){
            $res["merk_tipe_status"] = $data["merk_tipe_status"] ? $data["merk_tipe_status"] : "Sesuai";
            $res["merk_tipe_sensus"] = $data["merk_tipe_sensus"] ? $data["merk_tipe_sensus"] : "";
            $res["nomor_polisi_status"] = $data["nomor_polisi_status"] ? $data["nomor_polisi_status"] : "Sesuai";
            $res["nomor_polisi_sensus"] = $data["nomor_polisi_sensus"] ? $data["nomor_polisi_sensus"] : "";
            $res["nomor_rangka_status"] = $data["nomor_rangka_status"] ? $data["nomor_rangka_status"] : "Sesuai";
            $res["nomor_rangka_sensus"] = $data["nomor_rangka_sensus"] ? $data["nomor_rangka_sensus"] : "";
            $res["nomor_bpkb_status"] = $data["nomor_bpkb_status"] ? $data["nomor_bpkb_status"] : "Sesuai";
            $res["nomor_bpkb_sensus"] = $data["nomor_bpkb_sensus"] ? $data["nomor_bpkb_sensus"] : "";
        }

        //kib a,c,d,f
        if($f1 == "1" && $f2 =="3" && ($f == "1" || $f == "3" || $f == "4" || $f == "6") ){
            $res["alamat_status"] = $data["alamat_status"] ? $data["alamat_status"] : "Sesuai";
            $res["alamat_sensus"] = $data["alamat_sensus"] ? $data["alamat_sensus"] : "";
            $res["kampung_komplek_sensus"] = $data["kampung_komplek_sensus"] ? $data["kampung_komplek_sensus"] : "";
            $res["kelurahan_sensus"] = $data["kelurahan_sensus"] ? $data["kelurahan_sensus"] : "";
            $res["kode_kelurahan_sensus"] = $data["kode_kelurahan_sensus"] ? $data["kode_kelurahan_sensus"] : "";
            $res["kecamatan_sensus"] = $data["kecamatan_sensus"] ? $data["kecamatan_sensus"] : "";
            $res["kota_sensus"] = $data["kota_sensus"] ? $data["kota_sensus"] : "";
            $res["status_pemilik_tanah"] = $data["status_pemilik_tanah"] ? $data["status_pemilik_tanah"] : "";
            $res["nama_pemilik_tanah"] = $data["nama_pemilik_tanah"] ? $data["nama_pemilik_tanah"] : "";
            $res["tanah_nibar"] = $data["tanah_nibar"] ? $data["tanah_nibar"] : "";
            $res["alasan_perubahan_luas"] = $data["alasan_perubahan_luas"] ? $data["alasan_perubahan_luas"] : "";
        }

        // kib d
        if($f1 == "1" && $f2 =="3" && $f == "4"){
            $res["jenis_perkerasan_status"] = $data["jenis_perkerasan_status"] ? $data["jenis_perkerasan_status"] : "Sesuai";
            $res["jenis_perkerasan_sensus"] = $data["jenis_perkerasan_sensus"] ? $data["jenis_perkerasan_sensus"] : "";
            $res["jenis_bahan_struktur_status"] = $data["jenis_bahan_struktur_status"] ? $data["jenis_bahan_struktur_status"] : "Sesuai";
            $res["jenis_bahan_struktur_sensus"] = $data["jenis_bahan_struktur_sensus"] ? $data["jenis_bahan_struktur_sensus"] : "";
            $res["nomor_ruas_jalan_status"] = $data["nomor_ruas_jalan_status"] ? $data["nomor_ruas_jalan_status"] : "Sesuai";
            $res["nomor_ruas_jalan_sensus"] = $data["nomor_ruas_jalan_sensus"] ? $data["nomor_ruas_jalan_sensus"] : "";
            $res["nomor_jaringan_irigasi_status"] = $data["nomor_jaringan_irigasi_status"] ? $data["nomor_jaringan_irigasi_status"] : "Sesuai";
            $res["nomor_jaringan_irigasi_sensus"] = $data["nomor_jaringan_irigasi_sensus"] ? $data["nomor_jaringan_irigasi_sensus"] : "";
        }


        return $res;
    }

    private function dataInventaris($nibar)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE nibar = :nibar";
        $kondisi = "SELECT kondisi FROM " .$this->table_dtawal. " WHERE nibar = :nibar";
        $sqlKondisi = $this->db->query($kondisi, array("nibar" => $nibar))->getRowArray();
        $sqlInventaris = $this->db->query($query, array("nibar" => $nibar))->getRowArray();

        $rincianInventaris = $this->mappingRincianInventaris($sqlInventaris);
        return $rincianInventaris;
    }

    public function newInventaris($nibar)
    {
        $cariDataAwal = $this->cariDataAwal($nibar);
        $dtInventaris = $this->dataInventaris($nibar);
        if($cariDataAwal){
            $res = array_merge($cariDataAwal, $dtInventaris);
            // $data = array(
            //     "nibar" => $res["nibar"],
            //     "idbi" => $res["idbi"]
            // );
            return $res;
        }else{
            return false;
        }
    }

}