<?php

class refBarang
{
    private $db;
    private $table = 'ref_barang';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getBarang($type='fetch',$wheres = false, $wheresIn = false, $likes = false, $page = 0, $limit = 5)
    {
        $offset = $page * $limit;
        $data = array();
        $conditions = array(); // Array to hold individual conditions

        $sql = "SELECT f1,f2,f,f,g,h,i,j,nm_barang,masa_manfaat FROM " . $this->table ;

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

        // Combine conditions with AND
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if($type == 'fetch'){
            $sql .= " LIMIT " .$offset. ',' .$limit;
            $result = $this->db->query($sql, $data)->getResultArray();
        }else{
            $result = $this->db->query($sql, $data)->rowCount();
        }
        return $result;
    }

}