<?php

include 'refbarang.php';
require('./libraries/FormValidaiton.php');

class refbarangView
{

    private $refbarang;
    private $validation;
    private $db;

    public function __construct($db)
    {
        $this->refbarang = new refBarang($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    private function processKdbrg($kdbrg)
    {
        $parts = explode(".", $kdbrg);
        $fields = ['f1', 'f2', 'f', 'g', 'h', 'i', 'j']; // Pastikan jumlah elemen mencukupi

        $wheres = array();
        foreach ($parts as $index => $value) {
            if (isset($fields[$index])) {
                $wheres[$fields[$index]] = $value;
            }
        }
        return $wheres;
    }

    private function filterWheres()
    {
        $arrayParams = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        $allowedParams = ["kdbrg" => ""];
        $filteredParams = array_intersect_key($arrayParams, $allowedParams);

        $wheres = array();

        if (isset($filteredParams['kdbrg'])) {
            $kdbrg = $filteredParams['kdbrg'];
            $wheres = $this->processKdbrg($kdbrg);
        }

        return $wheres;
    }

    private function filterLikes()
    {
        $arrayParams = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        $allowedParams = ["nm_barang" => ""];
        $filteredParams = array_intersect_key($arrayParams, $allowedParams);

        $likes = array();
        if(count($filteredParams) > 0){
            foreach($filteredParams as $key => $val){
                $likes[$key] = $val;
            }
        }
        return $likes;
    }

    public function getBarang()
    {
        $offset = isset($_GET['offset']) ? sanitize($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? sanitize($_GET['limit']) : 10;
        $offset = filter_var($offset, FILTER_VALIDATE_INT) ? (int)$offset : 0;
        $limit = filter_var($limit, FILTER_VALIDATE_INT) ? (int)$limit : 10;

        $wheres = $this->filterWheres();
        $wheresIn = array();
        $likes = $this->filterLikes();

        $datas = $this->refbarang->getBarang('fetch',$wheres, $wheresIn, $likes, $offset, $limit);
        $count = $this->refbarang->getBarang('count',$wheres, $wheresIn, $likes, $offset, $limit);
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

$refbarangView = new refbarangView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        $refbarangView->getBarang();
        break;

    default:
        $refbarangView->notFound();
        break;
}