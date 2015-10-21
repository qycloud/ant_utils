<?php
namespace Utils;

class Singleton
{
    private static $_instances = array();

    final public static function instance()
    {
        $className = get_called_class();
        if (!isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className();
        }
        return self::$_instances[$className];
    }

    final private function __clone()
    {
    }
}