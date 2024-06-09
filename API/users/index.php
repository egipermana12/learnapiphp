<?php

include 'Users.php';
require('./libraries/FormValidaiton.php');

class UsersView
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

    public function register()
    {
        $input = json_decode(file_get_contents('php://input'), TRUE);
        $error = false;

        $this->validation->setRules('uid', 'UID', 'trim|required|unique[user]');
        $this->validation->setRules('name', 'Name', 'trim|required');
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
                $setter = 'set' . ucfirst($key);
                $db_data[$key] = $this->users->$setter($value);
            }

            $save = $this->users->registerUser($db_data);

            if($save){
                 http_response_code(201);
                $data = [
                    "message" => "Registrasi user berhasil"
                ];
                echo json_encode($data);
            }else{
                http_response_code(503);
                echo json_encode(array("message" => "Failed save data!"));
            }
        }
    }

    public function notFound(){
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }
}

$usersView = new UsersView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'POST':
        $usersView->register();
        break;

    default:
        $usersView->notFound();
        break;
}