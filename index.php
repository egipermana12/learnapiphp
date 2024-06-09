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

//token
include 'libraries/middlewareToken.php';

$db = new Database();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$token = new middlewareToken($db);


$pg = isset($_GET['page']) ? $_GET['page'] : null ;

switch($pg) {
    case 'employee': {
        $token->checkBearerToken();
        include('API/employe/index.php');
        break;
    }
    case 'register': {
        include('API/users/index.php');
        break;
    }
    case 'login': {
        include('API/users/login.php');
        break;
    }
    default: {
        include('API/index.php');
        break;
    }
}
