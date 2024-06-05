<?php

include 'employees.php';
require('./libraries/FormValidaiton.php');

class EmployeeView{
    private $employess;
    private $validation;
    private $db;

    public function __construct($db){
        $this->employess = new Employees($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    private function getFilterLikes(){
        $likes = array();
        if(isset($_GET['name'])){
            $likes['name'] = $this->employess->setName($_GET['name']);
        }
        if(isset($_GET['email'])){
            $likes['email'] = $this->employess->setEmail($_GET['email']);
        }
        return $likes;
    }

    private function getFilterWhereIn(){
        $wheres = array();
        if(isset($_GET['ids'])){
            $wheres['id'] = explode(",", $_GET['ids']);
        }
        return $wheres;
    }

    public function getAllData(){
        $offset = isset($_GET['offset']) ? sanitize($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? sanitize($_GET['limit']) : 3;
        $offset = filter_var($offset, FILTER_VALIDATE_INT) ? (int)$offset : 0;
        $limit = filter_var($limit, FILTER_VALIDATE_INT) ? (int)$limit : 3;

        $wheres = array();
        $wheresIn = $this->getFilterWhereIn();
        $likes = $this->getFilterLikes();

        $datas = $this->employess->getEmployees($wheres, $wheresIn, $likes, $offset, $limit);

        if(count($datas) > 0){
            http_response_code(200);
            $data = [
                "message" => "Data ditampilkan",
                "total" => count($datas),
                "limit" => $limit,
                "data" => $datas
            ];
            echo json_encode($data);
        }else{
            http_response_code(404);
            echo json_encode(array("message" => "No record found."));
        }
    }

    public function getOneData($id){
        $result = $this->employess->getSingleData($id);
        if($result){
            http_response_code(200);
            $data = [
                "message" => "Data ditampilkan",
                "data" => $result
            ];
            echo json_encode($data);
        }else{
            http_response_code(404);
            echo json_encode(array("message" => "No record found."));
        }
    }

    public function saveData(){
        if(isset($_SERVER['REQUEST_METHOD']) == "POST"){
            if($_POST){
                $error = false;
                //validasi
                $this->validation->setRules('name', 'Name', 'trim|required');
                $this->validation->setRules('email', 'Email', 'trim|required|unique[Employee]');

                $valid = $this->validation->validate();

                if(!$valid){
                    $error = true;
                    http_response_code(503);
                    $data = [
                        "status" => "Error",
                        "message" => $this->validation->getMessage()
                    ];
                    echo json_encode($data);
                }
                if(!$error){
                    $db_data = [];
                    foreach ($_POST as $key => $value) {
                        $setter = 'set' . ucfirst($key);
                        $db_data[$key] = $this->employess->$setter($value);
                    }
                    $save = $this->employess->createData($db_data);

                    if($save){
                        http_response_code(201);
                        $data = [
                            "message" => "Data berhasil ditambahkan"
                        ];
                        echo json_encode($data);
                    }else{
                        http_response_code(404);
                        echo json_encode(array("message" => "Failed save data!"));
                    }
                }
            }
        }else{
            http_response_code(501);
            echo json_encode(array("status" => "Bad Request","message" => "This API Method must contain valid POST Data!"));
        }
    }

    public function updateData($id){
        if($_SERVER['REQUEST_METHOD'] == "PUT"){
            $result = $this->employess->getSingleData($id);

            if(!$result){
                http_response_code(404);
                echo json_encode(array("message" => "No record found."));
            }

            $error = false;

            if(!$error){
                $input = (array) json_decode(file_get_contents('php://input'), TRUE);
                $db_data = [];
                foreach ($input as $key => $value) {
                    $setter = 'set' . ucfirst($key);
                    $db_data[$key] = $this->employess->$setter($value);
                }
                $db_where['id'] = $id;

                $save = $this->employess->updateData($db_data, $db_where);

                if($save){
                    http_response_code(200);
                    $data = [
                        "message" => "Data berhasil diupdate"
                    ];
                    echo json_encode($data);
                }else{
                    http_response_code(404);
                    echo json_encode(array("message" => "Failed update data!"));
                }
            }

        }else{
            die();
        }
    }

    public function deleteData($id){
        if($_SERVER['REQUEST_METHOD'] == "DELETE"){
           $result = $this->employess->getSingleData($id);

            if(!$result){
                http_response_code(404);
                echo json_encode(array("message" => "No record found."));
            }

            $db_data['id'] = $$id;
            $delete = $this->employess->deleteData($db_data);

            if($delete){
                http_response_code(200);
                $data = [
                    "message" => "Data berhasil dihapus"
                ];
                echo json_encode($data);
            }else{
                http_response_code(404);
                echo json_encode(array("message" => "Failed delete data!"));
            }
        }else{
            die();
        }
    }

    public function notFound(){
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }
}

$EmployeeView = new EmployeeView($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : '' ;

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if($id){
            $EmployeeView->getOneData($id);
        }else{
            $EmployeeView->getAllData();
        }
        break;

    case 'POST':
        $EmployeeView->saveData();
        break;

    case 'PUT':
        $EmployeeView->updateData($id);
        break;

    case 'DELETE':
        $EmployeeView->deleteData($id);
        break;

    default:
        $EmployeeView->notFound();
        break;
}