<?php

namespace App\Classes;

class ShortIdGenerator
{
    public function generateId($length) {
        $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char = str_shuffle($char);
        for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
            $rand .= $char{mt_rand(0, $l)};
        }
        return $rand;
    }
}
