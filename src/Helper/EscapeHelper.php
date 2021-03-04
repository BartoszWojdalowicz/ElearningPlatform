<?php

namespace App\Helper;

class EscapeHelper
{

    public function arrayEscape(Array $array): array
    {
        $array=array_map(static function ($val){
            return is_string($val)?htmlspecialchars($val,ENT_QUOTES):$val;
        }, $array);
        return $array;
    }



}