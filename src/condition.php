<?php

namespace Utils;

use Utils\Condition\Operator;

/**
 * 条件 接口
 * @todo 封装条件解析
 * @package  lib.workflow
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */
class Condition
{
    protected $input;
    protected $output;
    protected $op;

    public function __construct($input, $op, $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->op = Operator::factory($op);
    }

    /**
     * 条件计算
     */
    public function evaluate()
    {
        return $this->op->compare($this->input, $this->output);
    }
}
