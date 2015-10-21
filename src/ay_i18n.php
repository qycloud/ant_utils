<?php
namespace Utils;

/**
 * Internationalization (ay_i18n) class. Provides language loading and translation
 * methods without dependancies on [gettext](http://php.net/gettext).
 *
 * Typically this class would never be used directly, but used via the i18n()
 * function, which loads the message and replaces parameters:
 *
 *     // Display a translated message
 *     echo i18n('Hello, world');
 *
 *     // With parameter replacement
 *     echo i18n('Hello, :user', array(':user' => $username));
 *
 * [!!] The i18n() function is declared in `SYSPATH/base.php`.
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class ay_i18n
{

    /**
     * @var  string   target language: en-us, es-es, zh-cn, etc
     */
    public static $lang = 'zh-cn';

    // Cache of loaded languages
    protected static $_cache = array();

    /**
     * Get and set the target language.
     *
     *     // Get the current language
     *     $lang = I18n::lang();
     *
     *     // Change the current language to Spanish
     *     I18n::lang('es-es');
     *
     * @param   string   new language setting
     * @return  string
     * @since   3.0.2
     */
    public static function lang($lang = NULL)
    {
        if ($lang) {
            // Normalize the language
            ay_i18n::$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
        }

        return ay_i18n::$lang;
    }

    /**
     * Returns translation of a string. If no translation exists, the original
     * string will be returned. No parameters are replaced.
     *
     *     $hello = I18n::get('Hello friends, my name is :name');
     *
     * @param   string   text to translate
     * @return  string
     */
    public static function get($string)
    {
        if (!isset(ay_i18n::$_cache[ay_i18n::$lang]) && !isset(ay_i18n::$_cache[ay_i18n::$lang][$string])) {

            ay_i18n::load($string);
        }

        // Return the translated string if it exists
        return isset(ay_i18n::$_cache[ay_i18n::$lang][$string]) ? ay_i18n::$_cache[ay_i18n::$lang][$string] : $string;
    }

    /**
     * Returns the translation table for a given language.
     *
     *     // Get all defined Spanish messages
     *     $messages = I18n::load('es-es');
     *
     * @param   string   language to load
     * @return  array
     */
    public static function load($string)
    {
        $lang = ay_i18n::$lang;
        if (isset(ay_i18n::$_cache[ay_i18n::$lang]) && isset(ay_i18n::$_cache[ay_i18n::$lang][$string])) {
            return ay_i18n::$_cache[ay_i18n::$lang][$string];
        }
        // New translation table
        $table = array();
        if (isset(ay_i18n::$_cache[ay_i18n::$lang])) {
            $table = ay_i18n::$_cache[ay_i18n::$lang];
        }
        // Split the language: language, region, locale, etc
        $parts = explode('-', $lang);

        $fileName = strstr($string, '.', TRUE);
        do {
            // Create a path for this set of parts
            $path = implode(DIRECTORY_SEPARATOR, $parts);

            $file = \a_y::find_file('i18n', $path.'/'.$fileName, NULL, TRUE);

            if (!empty($file[0])) {
                $table = array_merge($table, \a_y::load($file[0]));
            }
            // Remove the last part
            array_pop($parts);
        } while ($parts);

        // Cache the translation table locally
        return ay_i18n::$_cache[$lang] = $table;
    }

    function __construct()
    {
        // This is a static class
    }

} // End I18n
