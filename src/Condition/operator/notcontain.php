<?php

namespace Utils\Condition\Operator;

use Utils\Condition\Operator;

class Notcontain extends Operator
{
    public function compare($value1, $value2)
    {
        if (is_array($value1)) {
            $notContain = $value2 ? true : false;
            foreach ($value2 as $vo) {
                if (in_array($vo, $value1)) {
                    $notContain = false;
                }
            }
            return $notContain;
        } elseif (is_string($value1)) {
            return !in_array($value2, $value1);
        }
    }
}