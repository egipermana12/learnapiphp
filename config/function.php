<?php

function sanitize($params)
{
    return htmlspecialchars(strip_tags($params));
}

function isUnique($input, $result, $table)
{
    $isUnique = '';
    if($input == $result)
    {
        $isUnique = 'required';
    }else{
        $isUnique = 'required|unique['.$table.']';
    }
    return $isUnique;
}