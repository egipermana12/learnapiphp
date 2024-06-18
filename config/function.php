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