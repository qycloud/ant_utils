<?php
namespace Utils;
class A_y
{
    const VERSION  = '1.1';
    const CODENAME = 'AYSaaS';

    const ERROR = 'ERROR';
    const DEBUG = 'DEBUG';

    const FILE_SECURITY = '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');';

    protected static $_paths = array(DOCROOT);

    public static $log;

    public static $config;

    public static $levels = array(
        E_ERROR  => 'Error',
        E_WARNING => 'Warning',
        E_PARSE  => 'Parsing Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice'
    );

    public static $php_errors = array(
        E_ERROR  => 'Error',
        E_PARSE  => 'Parsing Error'
    );

    public static $log_errors = array(
        E_WARNING => 'Warning',
        E_ERROR  => 'Error',
        E_PARSE  => 'Parsing Error'
    );

    protected static $_sa = array(
        '15950468741'
    );

    protected static $_init = FALSE;

    public function __construct()
    {

    }

    public static function init()
    {
        if (a_y::$_init) {
            return;
        }

        static::$_init = TRUE;
        static::$log = \Utils\Log\ay_log::instance();
        static::$config = \Utils\config\ay_config::instance();

    }

    public static function load($filepath= null)
    {
        return include $filepath;
    }

    public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
    {
        $ext = ($ext === NULL) ? EXT : '.'.$ext;
        $path = $dir.DIRECTORY_SEPARATOR.$file.$ext;
        if ($array OR $dir === 'config' OR $dir === 'i18n') {
            $paths = a_y::$_paths;
            $found = array();
            foreach ($paths as $dir) {
                if (is_file($dir.$path)) {
                    $found[] = $dir.$path;
                }
            }
        } else {
            $found = FALSE;
            foreach (a_y::$_paths as $dir) {
                if (is_file($dir.$path)) {
                    $found = $dir.$path;
                    break;
                }
            }
        }
        return $found;
    }


    public static function module(array $modules = null)
    {
        if ($modules === NULL) {
            return null;
        }

        foreach ($modules as $name => $path) {
            if (!file_exists($path)) {
                continue;
            }
            include $path;
        }

        return true;
    }

    public static function config($group)
    {
        static $config;

        if (strpos($group, '.') !== FALSE) {
            list ($group, $path) = explode('.', $group, 2);
        }

        if (!isset($config[$group])) {
            $config[$group] = a_y::$config->load($group);
        }

        if (isset($path)) {
            return a_y::path($config[$group], $path);
        } else {
            return $config[$group];
        }
    }


    public static function include_paths()
    {
        Debug::showIncludedFiles();
    }


    public static function auto_load($class)
    {
        $file = str_replace('_', '/', strtolower($class));

        if ($path = a_y::find_file('model', $file)) {
            require $path;
            return TRUE;
        }

        return FALSE;
    }


    public static function LogaddPhp($err = array())
    {
        if (isset($err['file'])) {
            //替换掉系统路径
            $err['file'] = str_replace(DOCROOT, '***/', $err['file']);
        }

        static::$log->add(
            $err['type'],
            \Utils\Log\ay_Log_Db::$msgTmp,
            array(
                ':message' => $err['message'],
                ':file' => $err['file'],
                ':line' => $err['line'],
            )
        );

    }

    public static function trace($level, $message, $filepath, $line)
    {

        $severity = ( ! isset(static::$levels[$level])) ? $level : static::$levels[$level];

        $err = array(
            'type' => $severity,
            'message' => $message,
            'file' => $filepath,
            'line' => $line
        );
        self::LogaddPhp($err);
        self::SymfonyErrorHandler($level, $message, $filepath, $line);
        return true;
    }

    static function SymfonyErrorHandler($level, $message, $filepath, $line)
    {

        $handler = new Symfony\Component\HttpKernel\Debug\ErrorHandler();
        $handler->setLevel(null);

        $handler->handle($level, $message, $filepath, $line, '');
    }


    public static function error()
    {
        if ( ! $err = error_get_last()) {
            return;
        }

        $err['type'] = static::$levels[$err['type']];
        self::LogaddPhp($err);
        return TRUE;
    }

    public static function debug()
    {
        if (func_num_args() === 0) {
            return;
        }

        $variables = func_get_args();

        static::$log->add(static::DEBUG, print_r($variables, true));
    }

    public static function merge(array $a1, array $a2)
    {
        $result = array();
        for ($i = 0, $total = func_num_args(); $i < $total; $i++) {
            $arr = func_get_arg($i);

            foreach ($arr as $key => $val) {
                if (isset($result[$key])) {
                    if (is_array($val)) {
                        if (self::is_assoc($val)) {
                            $result[$key] = self::merge($result[$key], $val);
                        } else {
                            $diff = array_diff($val, $result[$key]);
                            $result[$key] = array_merge($result[$key], $diff);
                        }
                    } else {
                        $result[$key] = $val;
                    }
                } else {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }

    public static function get($array, $key, $default = NULL)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function is_assoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    public static function path($array, $path, $default = NULL)
    {
        $path = trim($path, '.* ');

        $keys = explode('.', $path);

        do {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                $key = (int) $key;
            }

            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } elseif ($key === '*') {
                if (empty($keys)) {
                    return $array;
                }

                $values = array();
                foreach ($array as $arr) {
                    if ($value = a_y::path($arr, implode('.', $keys))) {
                        $values[] = $value;
                    }
                }

                if ($values) {
                    return $values;
                } else {
                    break;
                }
            } else {
                break;
            }
        } while ($keys);

        return $default;
    }
}