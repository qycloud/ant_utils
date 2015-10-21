<?php

namespace Utils\Condition;

/**
 * OP操作类
 * @package  lib.workflow
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */
abstract class Operator
{
    /**
     * equal =
     * greater >
     * less <
     * notless >=
     * notgreater <=
     * notequal !=
     * contain contain
     * notcontain notcontain
     */
    final public static function factory($op)
    {
        try {
            $className = "Utils\\Condition\\Operator\\" . ucfirst($op);
            return new $className();

        } catch (\Exception $e) {
            throw new \Exception(sprintf('【s%】操作符不存在', $op));
        }
    }

    /**
     * 比较
     * @param mixed $value1
     * @param mixed $value2
     */
    abstract public function compare($value1, $value2);
}
