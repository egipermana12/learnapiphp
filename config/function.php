<?php

function sanitize($params){
    return htmlspecialchars(strip_tags($params));
}