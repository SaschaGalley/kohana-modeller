<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Modeller_I18n {

    /**
     * @var array Available languages
     */
    protected static $_available_languages = array();

    // -------------------------------------------------------------------------

    /**
     *
     */
    public static function available_languages($reset = FALSE)
    {
        if (empty(self::$_available_languages) OR $reset === TRUE)
        {
            self::$_available_languages = ($reset === TRUE) ? array() : Cache::instance()->get('available_languages', array());

            if (empty(self::$_available_languages))
            {
                $languages = ORM::factory('I18n_Language')->find_all()->as_array();

                foreach ($languages as $language)
                {
                    self::$_available_languages[$language->id] = $language->iso;
                }

                Cache::instance()->set('available_languages', self::$_available_languages, 1);
            }
        }

        return self::$_available_languages;
    }

    // -------------------------------------------------------------------------

    /**
     * Getter and Setter for current language
     *
     * @return int language id
     */
    public static function language($value = FALSE, $reset = FALSE)
    {
        if ($value === FALSE)
        {
            $value = I18n::lang();
        }

        // @TODO: add caching
        $lang = ($reset === TRUE) ? FALSE : Cache::instance()->get('language_'.$value, FALSE);

        if ($lang === FALSE)
        {
            $lang = ORM::factory('I18n_Language')->where(is_numeric($value) ? 'id' : 'iso', '=', $value)->find();

            if ( ! $lang->loaded())
            {
                throw new Kohana_Exception('The langugae :langugae does not exist in the database',
                    array(':langugae' => $value));
            }

            Cache::instance()->set('language_'.$value, $lang, 1);
        }

        return $lang;
    }

    // -------------------------------------------------------------------------

}