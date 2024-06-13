<?php

include 'refskpd.php';
require('./libraries/FormValidaiton.php');

class refSKPDView
{
    private $refskpd;
    private $validation;
    private $db;

    public function __construct($db)
    {
        $this->refskpd = new refSKPD($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    public function getAllSKPD()
    {
        $offset = isset($_GET['offset']) ? sanitize($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? sanitize($_GET['limit']) : 10;
        $offset = filter_var($offset, FILTER_VALIDATE_INT) ? (int)$offset : 0;
        $limit = filter_var($limit, FILTER_VALIDATE_INT) ? (int)$limit : 10;

        $wheres = array();
        $wheresIn = array();
        $likes = array();

        $datas = $this->refskpd->getAllSKPD('fetch',$wheres, $wheresIn, $likes, $offset, $limit);
        $count = $this->refskpd->getAllSKPD('count',$wheres, $wheresIn, $likes, $offset, $limit);
        if(count($datas) > 0){
            http_response_code(200);
            $data = [
                "message" => "Data ditampilkan",
                "total" => $count,
                "limit" => $limit,
                "data" => $datas
            ];
            echo json_encode($data);
        }else{
            http_response_code(404);
            echo json_encode(array("message" => "No record found."));
        }
    }

    public function notFound(){
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }

}

$refskpdView = new refSKPDView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        $refskpdView->getAllSKPD();
        break;

    default:
        $refskpdView->notFound();
        break;
}