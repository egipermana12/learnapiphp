<?php

class Users
{
    private $db;
    private $table = 'user';

    //atributes
    public $uid;
    public $name;
    public $password;
    public $token;
    public $groups;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param mixed $uid
     *
     * @return self
     */
    public function setUid($uid)
    {
        return $this->uid = sanitize($uid);
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        return $this->name = sanitize($name);
    }

    /**
     * @param mixed $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        return $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param mixed $token
     *
     * @return self
     */
    public function setToken($token)
    {
        return $this->token = $token;
    }

    /**
     * @param mixed $group
     *
     * @return self
     */
    public function setGroups($groups)
    {
        return $this->group = sanitize($groups);
    }

    public function registerUser($db_data)
    {
        $sql = $this->db->insert($this->table, $db_data);
        return $sql;
    }

    public function checkUser($uid)
    {
        $sql = "SELECT uid,password FROM " .$this->table. " WHERE uid= :uid";
        $result = $this->db->query($sql, array('uid' => $uid))->getRowArray();
        return $result;
    }

    function generateToken($length = 32) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public function updateToken($uid)
    {
        $db_data['tokens'] = $this->generateToken();
        $db_data['tgl_tokens'] = date('Y-m-d');
        $where['uid'] = $uid;
        $sql = $this->db->update($this->table, $db_data, $where);
        return $sql;
    }

    public function login($db_data)
    {
        $checkUser = $this->checkUser($db_data['uid']);
        if($checkUser){
            $checkPassword = password_verify($db_data['password'], $checkUser['password']);
            if($checkPassword){
                $updateToken = $this->updateToken($db_data['uid']);
                $sql = "SELECT uid,tokens FROM " .$this->table. " WHERE uid= :uid";
                $result = $this->db->query($sql, array('uid' => $db_data['uid']))->getRowArray();
            }else{
                $result = false;
            }
        }else{
            $result = false;
        }
        return $result;
    }

}