<?php

include 'setting.php';
require('./libraries/FormValidaiton.php');

class settingView
{
    private $setting;
    private $validation;
    private $db;

    public function __construct($db)
    {
        $this->setting = new Setting($db);
        $this->validation = new FormValidation();
        $this->db = $db;
    }

    public function getSetting()
    {
        $datas = $this->setting->getAllSetting();
        if(count($datas) > 0){
            http_response_code(200);
            $data = [
                "message" => "Data ditampilkan",
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

$settingView = new settingView($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        $settingView->getSetting();
        break;

    default:
        $settingView->notFound();
        break;
}