<?php
/**
 * 时间范围处理文件
 *
 * @package   Lib
 * @author    Tom.Huang <hzlhu.dargon@gmail.com>
 * @copyright Copyright (C) 2011 Safirst Technology (www.a-y.com.cn
 */

namespace Utils;
use \Carbon\Carbon;

class DateTimeRange extends \Model\Base
{
    private $_carbon;

    public function setTime($year = null, $month = null, $day = null,
        $hour = null, $minute = null, $second = null)
    {
        $this->_carbon = Carbon::create(
            $year, $month, $day, $hour, $minute, $second
        );
        return $this;
    }


    public function lastWeek()
    {
        $this->_carbon->subWeek();
        return $this->thisWeek();
    }

    public function thisDay()
    {
        return array(
            'start' => $this->_carbon->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->endOfDay()->toDateTimeString()
        );
    }

    public function thisWeek()
    {
        return array(
            'start' => $this->_carbon->addDays(
                1 - $this->_carbon->dayOfWeek
            )->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->addDays(6)->endOfDay()->toDateTimeString()
        );
    }

    public function lastMonth()
    {
        $this->_carbon->subMonth();
        return $this->thisMonth();
    }

    public function thisMonth()
    {
        return array(
            'start' => $this->_carbon->startOfMonth()
                ->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->endOfMonth()->endOfDay()
                ->toDateTimeString()
        );
    }

    public function thisQuarter()
    {
        if ($this->_carbon->quarter == 1) {
            $this->_carbon->month = 1;
        } else if ($this->_carbon->quarter == 2) {
            $this->_carbon->month = 4;
        } else if ($this->_carbon->quarter == 3) {
            $this->_carbon->month = 7;
        } else if ($this->_carbon->quarter == 4) {
            $this->_carbon->month = 10;
        }

        return array(
            'start' => $this->_carbon->startOfMonth()
                ->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->addMonths(2)->endOfMonth()->endOfDay()
                ->toDateTimeString()
        );
    }

    public function halfYear()
    {
        return array(
            'start' => $this->_carbon->subMonths(6)
                ->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->addMonths(6)->endOfDay()
                ->toDateTimeString()
        );
    }

    public function oneYear()
    {
        return array(
            'start' => $this->_carbon->subYear(1)
                ->startOfDay()->toDateTimeString(),
            'end' => $this->_carbon->addYear(1)->endOfDay()
                ->toDateTimeString()
        );
    }
}