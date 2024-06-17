<?php
/*
* Data awal inventaris
*/

include 'dataawal.php';
require('./libraries/FormValidaiton.php');

class dataAwalView
{
    private $datawal;
    private $validation;
    private $db;

    public function __construct($db)
    {
        $this->datawal = new dataAwal($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    public function wheresIn()
    {
        $where = array();

        $staset = $this->datawal->getStatusAsset();
        if(!empty($staset)){
            $where['a.staset'] = explode(",", $staset['nilai']);
        }

        $whereF2 = $this->datawal->pecahF2();
        if(!empty($whereF2)){
            $where['a.f2'] = $whereF2;
        }

        $whereF = $this->datawal->pecahF();
        if(!empty($whereF)){
            $where['a.f'] = $whereF;
        }

        return $where;
    }

    public function wheres()
    {
        $where = array();

        $where['a.f1'] = '1';

        //untuk handle params
        $arrayParams = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        $allowedParams = ["nibar" => ""];
        $filteredParams = array_intersect_key($arrayParams, $allowedParams);

        if(isset($filteredParams['nibar'])){
            $where['idawal'] = $filteredParams['nibar'];
        }

        return $where;
    }

    public function getAllBarang()
    {
        $offset = isset($_GET['offset']) ? sanitize($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? sanitize($_GET['limit']) : 10;
        $offset = filter_var($offset, FILTER_VALIDATE_INT) ? (int)$offset : 0;
        $limit = filter_var($limit, FILTER_VALIDATE_INT) ? (int)$limit : 10;

        $wheres = $this->wheres();
        $wheresIn = $this->wheresIn();
        $likes = array();

        $datas = $this->datawal->getAllData('fetch',$wheres, $wheresIn, $likes, $offset, $limit);
        $count = $this->datawal->getAllData('count',$wheres, $wheresIn, $likes, $offset, $limit);


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

    public function notFound()
    {
        http_response_code(404);
        echo json_encode(array("message" => "Page Not Found!"));
    }

}

$dataAwalView = new dataAwalView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        $dataAwalView->getAllBarang();
        break;

    default:
        $dataAwalView->notFound();
        break;
}