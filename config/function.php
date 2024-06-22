<?php

function sanitize($params)
{
    return htmlspecialchars(strip_tags($params));
}

function isUnique($input, $result, $table)
{
    $isUnique = '';
    if($input == $result)
    {
        $isUnique = 'required';
    }else{
        $isUnique = 'required|unique['.$table.']';
    }
    return $isUnique;
}

function mappingTableKib($f1, $f2, $f)
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

function getDetailsKib($table, $idbi)
{
    global $db;
    switch ($table) {
        case 'kib_a':
            $query = "SELECT luas, alamat, alamat_kel, alamat_a, alamat_b, alamat_c, alamat_d, penggunaan, ket FROM " .$table . " WHERE idbi = :idbi";
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();

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
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();
            return $res;
        break;
        case 'kib_c':
            $query = "SELECT luas_lantai,alamat,alamat_a,alamat_b,alamat_c,alamat_d,kota,alamat_kel,alamat_kec,luas,status_tanah,kode_tanah,ket FROM " .$table . " WHERE idbi = :idbi";
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();

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
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();
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
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();
            return $res;
            break;
            case 'kib_f':
            $query = "SELECT luas,alamat,alamat_a,alamat_b,alamat_c,alamat_d,kota,alamat_kel,alamat_kec,ket FROM " .$table . " WHERE idbi = :idbi";
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();
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
            $res = $db->query($query, array('idbi' => $idbi))->getRowArray();
            return $res;
        break;
    default:
        // code...
    break;
    }
}

function getKota($kdkota){
    global $db;
    $query = "SELECT nm_wilayah FROM ref_kotakec WHERE kd_kota = :kd_kota AND kd_kec = '0' AND kd_kel = '0' ";
    $res = $db->query($query, array('kd_kota' => $kdkota))->getRowArray();
    return $res;
}

function getKec($kdkota, $kdkec){
    global $db;
    $query = "SELECT nm_wilayah FROM ref_kotakec WHERE kd_kota = :kd_kota AND kd_kec = :kd_kec AND kd_kel = '0' ";
    $res = $db->query($query, array('kd_kota' => $kdkota, 'kd_kec' => $kdkec))->getRowArray();
    return $res;
}

function getKel($kdkota, $kdkec, $kdkel){
    global $db;
    $query = "SELECT nm_wilayah FROM ref_kotakec WHERE kd_kota = :kd_kota AND kd_kec = :kd_kec AND kd_kel = :kd_kel ";
    $res = $db->query($query, array('kd_kota' => $kdkota, 'kd_kec' => $kdkec, 'kd_kel' => $kdkel))->getRowArray();
    return $res;
}