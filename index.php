<?php

include 'config/constant.php';
include 'config/function.php';

if(ENVIRONMENT == 'production') {
    error_reporting(0);
}else{
    error_reporting( E_ALL );
}


include 'config/database.php';
include 'config/'.strtolower($database['driver']).'.php';

$db = new Database();

$pg = isset($_GET['page']) ? $_GET['page'] : null ;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

switch($pg) {
    case 'employee': {
        include('API/employe/index.php');
        break;
    }
    default: {
        include('API/index.php');
        break;
    }
}
