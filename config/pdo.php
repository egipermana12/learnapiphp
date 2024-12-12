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

    public function wheresWithOperator($wheres = false){
        if ($wheres) {
            $str_wheres = [];
            foreach ($wheres as $field => $val) {
                // Memeriksa apakah $val mengandung operator
                if (is_array($val) && count($val) === 2) {
                    $operator = $val[0];
                    $value = $val[1];
                    $str_wheres[] = $field . ' ' . $operator . ' ?';
                } else {
                    // Default ke '=' jika operator tidak diberikan
                    $str_wheres[] = $field . ' = ?';
                    $value = $val;
                }
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
        try {
            // Validasi input
            if (empty($table) || empty($data) || !is_array($data)) {
                throw new InvalidArgumentException('Invalid table name or data.');
            }
    
            // Persiapan kolom dan placeholder
            $columns = implode(',', array_keys($data));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
    
            // Query SQL
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    
            // Prepare statement
            $stmt = $this->pdo->prepare($sql);
    
            // Eksekusi statement
            $stmt->execute(array_values($data));
    
            // Kembalikan ID terakhir yang dimasukkan (jika ada auto-increment)
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            // Tangani error PDO
            error_log($e->getMessage());
            return false;
        } catch (Exception $e) {
            // Tangani error umum
            error_log($e->getMessage());
            return false;
        }
    }

    public function bulkInsert($table, $dataSet)
    {
        try {
            if (empty($table) || empty($dataSet) || !is_array($dataSet)) {
                throw new InvalidArgumentException('Invalid table name or data set.');
            }
    
            // Persiapkan query
            $columns = implode(',', array_keys($dataSet[0]));
            $placeholders = implode(',', array_fill(0, count($dataSet[0]), '?'));
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
    
            // Eksekusi untuk setiap data
            foreach ($dataSet as $data) {
                $stmt->execute(array_values($data));
            }
    
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update($table, $data, $where = [])
    {
        try {
            // Validasi input
            if (empty($table) || empty($data) || !is_array($data)) {
                throw new InvalidArgumentException('Invalid table name or data.');
            }
    
            // Mempersiapkan kolom untuk diupdate
            $set = [];
            foreach ($data as $field => $val) {
                $set[] = "{$field} = ?";
            }
            $setQuery = implode(', ', $set);
    
            // Mempersiapkan kondisi WHERE jika diberikan
            $whereQuery = '';
            $params = array_values($data);
    
            if (!empty($where)) {
                if (is_array($where)) {
                    $conditions = [];
                    foreach ($where as $field => $val) {
                        $conditions[] = "{$field} = ?";
                        $params[] = $val;
                    }
                    $whereQuery = ' WHERE ' . implode(' AND ', $conditions);
                } else {
                    throw new InvalidArgumentException('Invalid WHERE clause format. Must be an array.');
                }
            }
    
            // Query SQL
            $sql = "UPDATE {$table} SET {$setQuery}{$whereQuery}";
    
            // Prepare statement
            $stmt = $this->pdo->prepare($sql);
    
            // Eksekusi statement
            $stmt->execute($params);
    
            // Mengembalikan jumlah baris yang diupdate
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Tangani error PDO
            error_log($e->getMessage());
            return false;
        } catch (Exception $e) {
            // Tangani error umum
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($table, $where = [])
    {
        try {
            // Validasi input
            if (empty($table)) {
                throw new InvalidArgumentException('Table name cannot be empty.');
            }
    
            // Siapkan kondisi WHERE jika diberikan
            $whereQuery = '';
            $params = [];
    
            if (!empty($where)) {
                if (is_array($where)) {
                    $conditions = [];
                    foreach ($where as $field => $val) {
                        $conditions[] = "{$field} = ?";
                        $params[] = $val;
                    }
                    $whereQuery = ' WHERE ' . implode(' AND ', $conditions);
                } else {
                    throw new InvalidArgumentException('Invalid WHERE clause format. Must be an array.');
                }
            }
    
            // Query SQL
            $sql = "DELETE FROM {$table}{$whereQuery}";
    
            // Prepare statement
            $stmt = $this->pdo->prepare($sql);
    
            // Eksekusi statement
            $stmt->execute($params);
    
            // Mengembalikan jumlah baris yang dihapus
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Tangani error PDO
            error_log($e->getMessage());
            return false;
        } catch (Exception $e) {
            // Tangani error umum
            error_log($e->getMessage());
            return false;
        }
    }


}
