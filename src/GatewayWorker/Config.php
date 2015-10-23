<?php 

namespace Utils\GatewayWorker;
use Utils\GatewayWorker\Config\Store;
use Closure;

class Config
{
    public static function initialize(Closure $initializer)
    {
        $class_name = get_called_class();
        $initializer(new $class_name());
    }

    public function setDriver($driver)
    {
        Store::$driver = $driver;
    }

    public function setGateway($gateway)
    {
        Store::$gateway = $gateway;
    }

    public function setStorePath($path = '')
    {
        Store::$storePath = !$path ?
            sys_get_temp_dir().'/workerman-sender/' :
            $path;
    }

    public function setPre($pre = '')
    {
        $pre = $pre.':client:';
        Store::$pre = $pre;
    }
}