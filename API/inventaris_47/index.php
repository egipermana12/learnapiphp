<?php

include 'inventaris47.php';
require('./libraries/FormValidaiton.php');

class Inventaris47_View
{
    private $inventaris47;
    private $validation;
    private $db;

    public function __construct($db){
        $this->inventaris47 = new Inventaris47($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    public function getOneData($nibar)
    {
        $result =$this->inventaris47->newInventaris($nibar);
        if($result){
            http_response_code(200);
            $data = [
                "message" => "Data ditampilkan",
                "data" => $result
            ];
            echo json_encode($data, JSON_UNESCAPED_SLASHES);
        }else{
            http_response_code(404);
            echo json_encode(array("message" => "No record found."));
        }
    }

    public function notFound(){
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }

//batas
}

$inventaris47View = new Inventaris47_View($db);

$nibar = isset($_GET['nibar']) ? (int) $_GET['nibar'] : '' ;

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if($nibar){
            $inventaris47View->getOneData($nibar);
        }else{
            $inventaris47View->notFound();
        }
    break;
    default:
        $inventaris47View->notFound();
    break;
}
