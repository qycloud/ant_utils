<?php
namespace Utils;

if ( ! defined('SYSPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Benchmark Class
 *
 * This class enables you to mark points and calculate the time difference
 * between them.  Memory consumption can also be displayed.
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/ay_benchmark.html
 */
class ay_benchmark
{
    // Singleton static instance
    private static $_instance;

    public static function reset()
    {
        self::$_instance === NULL &&  self::$_instance = null;
    }

    public static function instance()
    {
        if (self::$_instance === NULL) {
            // Create a new instance
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * List of all ay_benchmark markers and when they were added
     *
     * @var array
     */
    var $marker = array();

    // --------------------------------------------------------------------

    /**
     * Set a ay_benchmark marker
     *
     * Multiple calls to this function can be made so that several
     * execution points can be timed
     *
     * @access    public
     * @param    string    $name    name of the marker
     * @return    void
     */
    function mark($name)
    {
        $this->marker[$name] = microtime();
    }

    // --------------------------------------------------------------------

    /**
     * Calculates the time difference between two marked points.
     *
     * If the first parameter is empty this function instead returns the
     * {elapsed_time} pseudo-variable. This permits the full system
     * execution time to be shown in a template. The output class will
     * swap the real value for this variable.
     *
     * @access    public
     * @param    string    a particular marked point
     * @param    string    a particular marked point
     * @param    integer    the number of decimal places
     * @return    mixed
     */
    function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
    {
        if ($point1 == '') {
            return '{elapsed_time}';
        }

        if (!isset($this->marker[$point1])) {
            return '';
        }

        if (!isset($this->marker[$point2])) {
            $this->marker[$point2] = microtime();
        }

        list($sm, $ss) = explode(' ', $this->marker[$point1]);
        list($em, $es) = explode(' ', $this->marker[$point2]);

        return number_format(($em + $es) - ($sm + $ss), $decimals);
    }
}
