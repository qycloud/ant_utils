<?php

namespace Utils\Condition;

use Utils\Condition;

/**
 * 逻辑 接口
 * @todo 封装逻辑类型和逻辑解析
 * @package  lib.workflow
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */
class Logic
{

    /**
     * 逻辑中包含的条件组
     * @var array
     */
    protected $_conditions = array();

    /**
     * 条件逻辑
     * @var string $type
     * and 且 全部成立
     * or 或 满足其中一个
     * ...后续扩展的逻辑
     */
    public $type;

    public function __construct($type = 'and')
    {
        $this->type = $type;
    }

    /**
     * 添加一个条件
     *
     * @param Condition $condition
     * @access public
     * @return $this
     */
    public function add(Condition $condition)
    {
        $this->_conditions[] = $condition;
        return $this;
    }

    /**
     * 清空条件
     * @return \Lib\Condition\Logic
     */
    public function clear()
    {
        $this->_conditions = array();
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 逻辑运算结果
     *
     * @access public
     * @return boolean
     */
    public function result()
    {
        return $this->{'_' . $this->type}();
    }

    /**
     * OR逻辑
     * @return boolean
     */
    protected function _or()
    {
        foreach ($this->_conditions as $condition) {

            if ($condition->evaluate()) {
                return true;
            }
        }

        return false;
    }

    /**
     * AND逻辑
     * @return boolean
     */
    protected function _and()
    {
        foreach ($this->_conditions as $condition) {

            if ( ! $condition->evaluate()) {
                return false;
            }
        }

        return true;
    }
}
