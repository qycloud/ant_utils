<?php
namespace Utils\Log;

/**
 * log 发送到LOG server
 *
 */
class Ay_Log_Send extends Ay_log_writer
{

    static private $_url = 'http://www.upserver.aysaas.com:30000/log';//http://www.up.com/log

    public static $logType = array();

    public static $msgTmp = '错误信息：:message, 文件：:file, 行数：:line';


    // Directory to place log files in
    protected $_dbConfig;

    public function __construct($dbConfig = null)
    {

        // Determine the directory path
        $this->_dbConfig = $dbConfig;
    }


    public function write(array $messages)
    {
        $inItems = array();
        foreach ($messages as $m) {
            if ($m) {
                $inItems[] = array(
                    'type' => $m['type'],
                    'body' => addslashes($m['body']),
                    'time' => $m['time']
                );
            }
        }

        if (!empty($inItems)) {
            $inItems = array_shift($inItems);
            //post server
            $curl = new \Lib\Curl();
            $result = $curl->post(
                self::$_url,
                [
                 'content' => json_encode($inItems),
                 'key' => $this->getKey()
                ]
            );

            return $result;
        }

    }


    public function getKey()
    {

        $mac = shell_exec("ifconfig | awk '/eth/{print $5}'");
        $ip = trim(`ifconfig eth0|grep -oE '([0-9]{1,3}\.?){4}'|head -n 1`);

        $mac = trim(str_replace(':', '.', $mac));
        $domain = trim(str_replace(':', '.', \Config('app.www_domain')));

        $key = $domain . ':' . $mac . ':' . \Config('app.application_name') . ':' .
            (empty($_SESSION['enterprise_id']) ? 'sys' : $_SESSION['enterprise_id']);




        return $key;

    }

}