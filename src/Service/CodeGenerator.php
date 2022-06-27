<?php

namespace App\Service;

class CodeGenerator
{
    public const RANDOM_STRING = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public function generateCode()
    {
        $str = '';
        $strlen = strlen(self::RANDOM_STRING);
        for ($i = 0; $i < $strlen; $i++) {
            $str .=  self::RANDOM_STRING[rand(0, $strlen - 1)];
        }
        return $str;
    }

}