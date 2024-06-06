<?php

class Employees {
    private $db;
    private $table = 'Employee';

    //atributes
    public $id;
    public $name;
    public $email;
    public $designation;
    public $created;

    public function __construct($db){
        $this->db = $db;
    }

    public function setName($name){
        return $this->name = sanitize($name);
    }

    public function setEmail($email){
        return $this->email = sanitize($email);
    }

    public function setDesignation($designation){
        return $this->designation = sanitize($designation);
    }

    public function setCreated($created){
        return $this->created = sanitize($created);
    }

    public function getEmployees($type='fetch',$wheres = false, $wheresIn = false, $likes = false, $page = 0, $limit = 5){
        $offset = $page * $limit;
        $sql = "SELECT * FROM " . $this->table ;
        $data = array();

        $conditions = array(); // Array to hold individual conditions

        // Handle LIKE conditions
        if($likes){
            $arrLike = $this->db->likes($likes);
            $conditions[] = $arrLike; // Add LIKE clause to conditions array
            foreach ($likes as $key => $value) {
                $data[] = "%".$value."%";
            }
        }

        // Handle WHERE IN conditions
        if($wheresIn){
            $arrWhereIn = $this->db->wheresIn($wheresIn);
            $conditions[] = $arrWhereIn['query']; // Add WHERE IN clause to conditions array
            $data = array_merge($data, $arrWhereIn['values']); // Merge with existing data
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

    public function getSingleData($id){
        $sql = "SELECT id,name,email,designation,created FROM " .$this->table. " WHERE id= :id" ;
        return $result = $this->db->query($sql, array('id' => $id))->getRowArray();
    }

    public function createData($db_data)
    {
        $sql = $this->db->insert($this->table, $db_data);
        return $sql;
    }

    public function updateData($db_data, $where)
    {
        $sql = $this->db->update($this->table, $db_data, $where);
        return $sql;
    }

    public function deleteData($db_data)
    {
        $sql = $this->db->delete($this->table, $db_data);
        return $sql;
    }

}