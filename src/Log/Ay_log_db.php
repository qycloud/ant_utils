<?php
namespace Utils\Log;

/**
 * File log writer. Writes out messages and stores them in a YYYY/MM directory.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Ay_Log_Db extends Ay_log_writer
{

    public static $logType = array();

    public static $msgTmp = '错误信息：:message, 文件：:file, 行数：:line';


    // Directory to place log files in
    protected $_dbConfig;

    public function __construct($dbConfig = null)
    {

        // Determine the directory path
        $this->_dbConfig = $dbConfig;
    }

    /**
     * db 写入sys
     * @param array $messages
     */
    public function write(array $messages)
    {
        $inItems = array();
        foreach ($messages as $m) {
            if ($m) {
                $inItems[] = "(NULL, '{$m['type']}', '" . addslashes($m['body']) . "', '', '{$m['time']}')";
            }
        }
        $inItems = implode(', ', $inItems);
        if (!empty($inItems)) {
            $sql = <<<SQL
            INSERT INTO `sys_log` (`id`, `type`, `meg`, `eid`, `time`) VALUES
                $inItems ;
SQL;

            \Dbio::execute($sql);
        }

    }

}