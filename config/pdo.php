<?php

class Database
{
    private $pdo;
    private $stmt;
    private $trans_status;
    private $trans_message;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        global $database; //referensi ke file database di config
        if($database['driver'] == 'PDO')
        {
            try{
                $this->pdo = new PDO('mysql:host=localhost;dbname=' .$database['database'], $database['username'], $database['password']);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(Exception $e){
                echo "Database could not be connected: " . $e->getMessage();
            }
        }
        return $this;
    }

    public function query($sql, $data = null)
    {
        if($data && !is_array($data)) {
            $data = [$data];
        }

        $this->trans_status = true;

        try{
            $this->stmt = $this->pdo->prepare($sql);
            $exec = $this->stmt->execute($data);
        }catch(Exception $e){
            $this->trans_message = $e->getMessage();
            echo "Found Error in quer " . $sql . " with message " . $e;
            $this->trans_status = false;
        }
        return $this;
    }

    public function getResultArray($type = 'assoc')
    {
        switch($type) {
            case 'assoc' :
                $fetch_type = PDO::FETCH_ASSOC;
                break;
            case 'object' :
                $fetch_type = PDO::FETCH_OBJ;
                break;
            case 'num' :
                $fetch_type = PDO::FETCH_NUM;
                break; 
        }
        return $this->stmt->fetchAll($fetch_type);
    }

    public function getRowArray($type = 'assoc')
    {
        switch($type) {
            case 'assoc' :
                $fetch_type = PDO::FETCH_ASSOC;
                break;
            case 'object' :
                $fetch_type = PDO::FETCH_OBJ;
                break;
        }
        return $this->stmt->fetch($fetch_type);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function likes($likes = false){
        if($likes){
            $str_likes = [];
            foreach($likes as $field => $val){
                $str_likes[] = $field . ' LIKE ?';
            }
            $str_likes = join(' AND ', $str_likes);
        }
        return $str_likes;
    }

    public function wheres($wheres = false){
        if($wheres){
            $str_wheres = [];
            foreach($wheres as $field => $val){
                $str_wheres[] = $field . ' = ? ' ;
            }
            $str_wheres = join(' AND ', $str_wheres);
        }
        return $str_wheres;
    }

    function wheresIn($wheres = false){
        if($wheres && is_array($wheres)){
            $str_wherein = [];
            $values = [];

            foreach($wheres as $field => $val){
                if(is_array($val) && count($val) > 0){
                    $placeholder = implode(',', array_fill(0, count($val), '?'));
                    $str_wherein[] = $field . ' IN (' . $placeholder . ')';
                    $values = array_merge($values, $val);
                }
            }

            $str_wherein = join(' AND ', $str_wherein);
            return ['query' => $str_wherein, 'values' => $values];
        }
        return false;
    }

    public function insert($table, $data)
    {
        $column = join(',', array_keys($data));
        foreach ($data as $v) {
            $q[] = '?';
        }
        $value_mask = join(',', $q);

        $sql = 'INSERT INTO ' . $table . '(' . $column . ') VALUES (' . $value_mask . ')';
        $this->query($sql, array_values($data));
        return $this->trans_status;
    }

    public function update($table, $data, $where = false){
        foreach($data as $field => $val){
            $set[] = $field . ' = ? ';
        }
        if($where){
            if(is_array($where)){
                $str_where = [];
                foreach($where as $field => $val){
                    $str_where[] = $field . ' = ? ';
                }
                $str_where = join(' AND ', $str_where);
                $data = array_merge(array_values($data), array_values($where));
            }else{
                $str_where = $where;
            }
        }
        $add_where = $where ? ' WHERE ' . $str_where : '';
        $sql = 'UPDATE ' . $table . ' SET ' . join(',', $set) . $add_where;

        $this->query($sql, array_values($data));
        return $this->trans_status;
    }

    public function delete($table, $where = false){
        $data_where = [];

        $str_where = '';
        if($where){
            if(is_array($where)){
                $arr_where = [];
                foreach($where as $field => $val){
                    $arr_where[] = $field . ' = ? ';
                }
                $str_where = join(' AND ', $arr_where);
                $data_where = array_values($where);
            }else{
                $str_where = $where;
            }
        }
        $sql_where = $str_where ? ' WHERE ' . $str_where : '';

        $sql = 'DELETE FROM ' . $table . $sql_where;
        $this->query($sql, $data_where);
        return $this->trans_status;
    }

}
