<?php

/*
simple rate limiter ambil dari lib orang
 */

class RateExceededException extends Exception {}

class SimpleRateLimiter
{
    private $prefix;
    public function __construct($token, $prefix = "rate")
    {
        $this->prefix = md5($prefix . $token);

        if(!isset($_SESSION['cache']))
        {
            $_SESSION['cache'] = array();
        }

        if(!isset($_SESSION['expiries']))
        {
            $_SESSION['expiries'] = array();
        }else{
            $this->expireSessionKeys();
        }
    }

    public function limitRequestPerMinutes($allowedRequest, $minutes)
    {
        $this->expireSessionKeys();
        $requests = 0;

        foreach ($this->getKeys($minutes) as $key) {
            $requestsInCurrentMinute = $this->getSessionKey($key);
            if (false !== $requestsInCurrentMinute) $requests += $requestsInCurrentMinute;
        }

        if(false == $requestsInCurrentMinute)
        {
            $this->setSessionKey($key, 1, ($minutes * 60 +1));
        }else{
            $this->increment($key, 1);
        }
        if ($requests > $allowedRequest) throw new RateExceededException;
    }

    private function getKeys($minutes)
    {
        $keys = array();
        $now = time();
        for ($time = $now - $minutes * 60; $time <= $now; $time += 60) {
            $keys[] = $this->prefix . date("dHi", $time);
        }
        return $keys;
    }

    private function increment($key, $inc)
    {
        $cnt = 0;
        if(isset($_SESSION['cache'][$key])){
            $cnt = $_SESSION['cache'][$key];
        }
        $_SESSION['cache'][$key] = $cnt + $inc;
    }

    private function setSessionKey($key, $val, $expiry)
    {
        $_SESSION['expiries'][$key] = time() + $expiry;
        $_SESSION['cache'][$key] = $val;
    }

    private function getSessionKey($key)
    {
        return isset($_SESSION['cache'][$key]) ? $_SESSION['cache'][$key] : false;
    }

    private function expireSessionKeys()
    {
        foreach($_SESSION['expiries'] as $key => $value){
            if(time() > $value){
                unset($_SESSION['cache'][$key]);
                unset($_SESSION['expiries'][$key]);
            }
        }
    }
//batas
}