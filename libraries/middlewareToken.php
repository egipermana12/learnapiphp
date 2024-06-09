<?php

class middlewareToken
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function checkToken($tokens)
    {
        $sql = "SELECT tokens from user WHERE tokens = :tokens";
        $result = $this->db->query($sql, array('tokens' => $tokens));

        if($result->rowCount() > 0){
            return $result->getRowArray();
        }else{
            return false;
        }
    }

    public function checkBearerToken()
    {
        $token = $this->getAuthorizationHeader();
        if (!isset($token)) {
            http_response_code(401);
            echo json_encode(array("message" => "Token not provided."));
            exit;
        }

        $validToken = $this->checkToken($token);

        if (!$validToken) {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid token."));
            exit;
        }

        // Token valid, lanjutkan ke endpoint berikutnya
        return $validToken;
    }
}