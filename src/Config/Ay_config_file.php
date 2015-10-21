<?php
namespace Utils\Config;

class Ay_config_file extends Ay_config_reader
{

    // Configuration group name
    protected $_configuration_group;

    // Has the config group changed?
    protected $_configuration_modified = FALSE;

    public function __construct($directory = 'config')
    {
        // Set the configuration directory name
        $this->_directory = trim($directory, '/');

        // Load the empty array
        parent::__construct();
    }

    /**
     * Load and merge all of the configuration files in this group.
     *
     *     $config->load($name);
     *
     * @param   string  configuration group name
     * @param   array   configuration array
     * @return  $this   clone of the current object
     * @uses    Kohana::load
     */
    public function load($group, array $config = NULL)
    {
        if ($files = \A_y::find_file($this->_directory, $group, NULL, TRUE)) {
            // Initialize the config array
            $config = array();

            foreach ($files as $file) {
                // Merge each ay_config_file to the configuration array
                $config = \A_y::merge($config, \A_y::load($file));
            }
        }

        return parent::load($group, $config);
    }

} // End Kohana_Config
