<?php

namespace App\Main\Helpers;


use PhpParser\Node\Scalar\String_;
use function PHPUnit\Framework\isEmpty;

class Common{

    function convertPrice($number, $decimals = 0, $decimal_separator = ',', $thousands_separator = '.'){
        return number_format($number, $decimals, $decimal_separator, $thousands_separator) ?? 0;
    }

    function formatDate($date, $formatCurrent = 'd/m/Y', $format = 'Y-m-d'){
        try {
            $newDate = \DateTime::createFromFormat($formatCurrent, $date)->format($format);
        } catch (\Throwable $th) {
            $newDate = null;
        }
        return $newDate;
    }
}

