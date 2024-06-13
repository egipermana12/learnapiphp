<?php

class refSKPD
{
    private $db;
    private $table = 'ref_skpd';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllSKPD($type='fetch',$wheres = false, $wheresIn = false, $likes = false, $page = 0, $limit = 5)
    {
        $offset = $page * $limit;
        $data = array();
        $conditions = array(); // Array to hold individual conditions

        $sql = "SELECT c1,c,d,e,e1,nm_skpd FROM " . $this->table ;

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