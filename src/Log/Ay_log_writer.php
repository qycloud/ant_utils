<?php
namespace Utils\Log;

/**
 * Log ay_log_writer abstract class. All [Kohana_Log] writers must extend this class.
 *
 * @package    Kohana
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Ay_log_writer
{

    /**
     * Write an array of messages.
     *
     *     $ay_log_writer->write($messages);
     *
     * @param   array  messages
     * @return  void
     */
    abstract public function write(array $messages);

    /**
     * Allows the ay_log_writer to have a unique key when stored.
     *
     *     echo $ay_log_writer;
     *
     * @return  string
     */
    final public function __toString()
    {
        return spl_object_hash($this);
    }

} // End Kohana_Log_Writer
