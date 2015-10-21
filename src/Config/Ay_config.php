<?php
namespace Utils\config;

/**
 * Wrapper for configuration arrays. Multiple configuration readers can be
 * attached to allow loading configuration from files, database, etc.
 *
 * @package    Kohana
 * @category   Configuration
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class ay_config
{

    // Singleton static instance
    protected static $_instance;

    /**
     *     $ay_config = ay_config::instance();
     *
     * @return  Kohana_Config
     */
    public static function instance()
    {
        if (self::$_instance === NULL) {
            // Create a new instance
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    // Configuration readers
    protected $_readers = array();

    /**
     * Attach a configuration reader. By default, the reader will be added as
     * the first used reader. However, if the reader should be used only when
     * all other readers fail, use `FALSE` for the second parameter.
     *
     *     $ay_config->attach($reader);        // Try first
     *     $ay_config->attach($reader, FALSE); // Try last
     *
     * @param   object   Kohana_Config_Reader instance
     * @param   boolean  add the reader as the first used object
     * @return  $this
     */
    public function attach(ay_config_reader $reader, $first = TRUE)
    {
        if ($first === TRUE) {
            // Place the log reader at the top of the stack
            array_unshift($this->_readers, $reader);
        } else {
            // Place the reader at the bottom of the stack
            $this->_readers[] = $reader;
        }

        return $this;
    }

    /**
     * Detach a configuration reader.
     *
     *     $ay_config->detach($reader);
     *
     * @param   object  Kohana_Config_Reader instance
     * @return  $this
     */
    public function detach(ay_config_reader $reader)
    {
        if (($key = array_search($reader, $this->_readers))) {
            // Remove the writer
            unset($this->_readers[$key]);
        }

        return $this;
    }

    /**
     * Load a configuration group. Searches the readers in order until the
     * group is found. If the group does not exist, an empty configuration
     * array will be loaded using the first reader.
     *
     *     $array = $ay_config->load($name);
     *
     * @param   string  configuration group name
     * @return  object  Kohana_Config_Reader
     * @throws  Kohana_Exception
     */
    public function load($group)
    {
        foreach ($this->_readers as $reader) {
            if ($config = $reader->load($group)) {
                // Found a reader for this configuration group
                return $config;
            }
        }

        // Reset the iterator
        reset($this->_readers);

        if (!is_object($config = current($this->_readers))) {
            throw new \Exception('No configuration readers attached');
        }

        // Load the reader as an empty array
        return $config->load($group, array());
    }


    public function loadMeage($group)
    {
        $configs = array();
        foreach ($this->_readers as $reader) {
            $config = $reader->load($group, array());
            if (is_object($config)) {
                $config = $config->as_array();
            }
            $configs = array_merge($configs, $config);
        }

        // Reset the iterator
        reset($this->_readers);

        return $configs;
    }

    /**
     * Copy one configuration group to all of the other readers.
     *
     *     $ay_config->copy($name);
     *
     * @param   string   configuration group name
     * @return  $this
     */
    public function copy($group)
    {
        // Load the configuration group
        $config = $this->load($group);

        foreach ($this->_readers as $reader) {
            if ($config instanceof $reader) {
                // Do not copy the ay_config to the same group
                continue;
            }

            // Load the configuration object
            $object = $reader->load($group, array());

            foreach ($config as $key => $value) {
                // Copy each value in the ay_config
                $object->offsetSet($key, $value);
            }
        }

        return $this;
    }

    final private function __construct()
    {
        // Enforce singleton behavior
    }

    final private function __clone()
    {
        // Enforce singleton behavior
    }

} // End Kohana_Config
