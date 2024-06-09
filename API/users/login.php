<?php

include 'Users.php';
require('./libraries/FormValidaiton.php');

class loginView
{
    private $users;
    private $validation;
    private $db;

    public function __construct($db)
    {
        $this->users = new Users($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), TRUE);
        $error = false;

        $this->validation->setRules('uid', 'UID', 'trim|required');
        $this->validation->setRules('password', 'Password', 'trim|required');

        $valid = $this->validation->validate($input);

        if(!$valid){
            $error = true;
            http_response_code(503);
            $data = [
                "status" => "Error",
                "message" => $this->validation->getMessage()
            ];
            echo json_encode($data);
        }else{
            $db_data = [];
            foreach ($input as $key => $value) {
                $db_data[$key] = $value;
            }
            $login = $this->users->login($db_data);

            if($login){
                http_response_code(200);
                $data = [
                    "message" => "Login berhasil",
                    "data" => $login
                ];
                echo json_encode($data);
            }else{
                http_response_code(503);
                echo json_encode(array("message" => "Username or Password wrong"));
            }
        }

    }

    public function notFound(){
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }
}

$loginView = new loginView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'POST':
        $loginView->login();
        break;

    default:
        $loginView->notFound();
        break;
}