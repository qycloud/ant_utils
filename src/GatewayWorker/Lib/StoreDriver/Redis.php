<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Utils\GatewayWorker\Lib\StoreDriver;
use Utils\GatewayWorker\Config\Store;

/**
 * Redis
 */

class Redis extends \Redis
{
    public function connect($ip, $port, $timeout)
    {
        parent::connect($ip, $port, $timeout);
        $this->setOption(self::OPT_PREFIX, Store::$pre);
    }

    public function increment($key)
    {
        return parent::incr($key);
    }
}
