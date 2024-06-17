<?php

date_default_timezone_set('Asia/Jakarta');
session_start();

include 'config/constant.php';
include 'config/function.php';


if(ENVIRONMENT == 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}else{
    error_reporting( E_ALL );
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

include 'config/database.php';
include 'config/'.strtolower($database['driver']).'.php';

//token
include 'libraries/middlewareToken.php';

//ratelimiter
include 'libraries/SimpleRateLimiter.php';

$db = new Database();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$token = new middlewareToken($db);

//ratelimiter
$ratelimiter = new SimpleRateLimiter($_SERVER["REMOTE_ADDR"]);

$limit = 100;               //  number of connections to limit user to per $minutes
$minutes = 1;               //  number of $minutes to check for.
$seconds = floor($minutes * 60);    //  retry after $minutes in seconds.

try {
    $ratelimiter->limitRequestPerMinutes($limit, $minutes);
} catch (RateExceededException $e) {
    header("HTTP/1.1 429 Too Many Requests");
    header(sprintf("Retry-After: %d", $seconds));
    $data = 'Rate Limit Exceeded ';
    die (json_encode($data));
}


$pg = isset($_GET['page']) ? $_GET['page'] : null ;

switch($pg) {
    case 'employee': {
        $token->checkBearerToken();
        include('API/employe/index.php');
        break;
    }
    case 'dataawal': {
        $token->checkBearerToken();
        include('API/dataawal/index.php');
        break;
    }
    case 'refbarang': {
        $token->checkBearerToken();
        include('API/refbarang/index.php');
        break;
    }
    case 'refskpd': {
        $token->checkBearerToken();
        include('API/refskpd/index.php');
        break;
    }
    case 'setting': {
        $token->checkBearerToken();
        include('API/setting/index.php');
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
