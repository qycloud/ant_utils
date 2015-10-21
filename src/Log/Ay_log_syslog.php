<?php
namespace Utils\Log;

/**
 * Syslog log writer.
 *
 * @package    Logging
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Ay_log_syslog extends Ay_log_writer
{

    // The ay_log_syslog identifier
    protected $_ident;

    protected $_syslog_levels = array('ERROR'    => LOG_ERR,
                                      'CRITICAL' => LOG_CRIT,
                                      'STRACE'   => LOG_ALERT,
                                      'ALERT'    => LOG_WARNING,
                                      'INFO'     => LOG_INFO,
                                      'DEBUG'    => LOG_DEBUG);

    /**
     * Creates a new ay_log_syslog logger.
     *
     * @see http://us2.php.net/openlog
     *
     * @param   string  ay_log_syslog identifier
     * @param   int     facility to log to
     * @return  void
     */
    public function __construct($ident = 'KohanaPHP', $facility = LOG_USER)
    {
        $this->_ident = $ident;

        // Open the connection to ay_log_syslog
        openlog($this->_ident, LOG_CONS, $facility);
    }

    /**
     * Writes each of the messages into the ay_log_syslog.
     *
     * @param   array   messages
     * @return  void
     */
    public function write(array $messages)
    {
        foreach ($messages as $message) {
            syslog($this->_syslog_levels[$message['type']], $message['body']);
        }
    }

    /**
     * Closes the ay_log_syslog connection
     *
     * @return  void
     */
    public function __destruct()
    {
        // Close connection to ay_log_syslog
        closelog();
    }

} // End Kohana_Log_Syslog