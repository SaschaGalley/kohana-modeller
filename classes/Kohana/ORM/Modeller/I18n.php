<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CRUD I18n Model
 *
 * @author Sascha Galley
 *
 */
class Kohana_ORM_Modeller_I18n extends ORM_Modeller {

    /**
     * @var array Available languages
     */
    protected static $_available_languages = array();

    /**
     * @var array I18n columns
     */
    protected $_i18n_columns = array();

    /**
     * @var array I18n data
     */
    protected $_i18n_data = array();

    // -------------------------------------------------------------------------

    /**
     *
     */
    public static function available_languages()
    {
        if (empty(self::$_available_languages))
        {
            self::$_available_languages = Cache::instance()->get('available_languages', array());

            if (empty(self::$_available_languages))
            {
                $languages = ORM::factory('I18n_Language')->find_all()->as_array();

                foreach ($languages as $language)
                {
                    self::$_available_languages[$language->id] = $language->iso;
                }

                Cache::instance()->set('available_languages', self::$_available_languages, 100);
            }
        }

        return self::$_available_languages;
    }

    // -------------------------------------------------------------------------

    /**
     * Prepares the model database connection, determines the table name,
     * and loads column information.
     *
     * @return void
     */
    protected function _initialize()
    {
        foreach ($this->_i18n_columns as $i18n)
        {
            $this->_has_one[$i18n] = array('model' => 'I18n', 'foreign_key' => $i18n);
        }

        parent::_initialize();
    }

    // -------------------------------------------------------------------------

    /**
     *
     */
    protected function _column_with_lang($column)
    {
        if (($seperator = strrpos($column, '_')) !== FALSE)
        {
            $check_column   = substr($column, 0, strrpos($column, '_'));
            $check_language = substr($column, strrpos($column, '_') + 1);

            if (in_array($check_language, self::available_languages()) AND in_array($check_column, $this->i18n_columns()))
            {
                return array('column' => $check_column, 'language' => $check_language);
            }
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------

    /**
     * Handles getting of column
     * Override this method to add custom get behavior
     *
     * @param   string $column Column name
     * @throws Kohana_Exception
     * @return mixed
     */
    public function get($column)
    {
        if (parent::__isset($column) AND ! in_array($column, $this->i18n_columns()))
        {
            // This helps us prevent nesting issues
            return parent::get($column);
        }

        if (($translation = $this->_column_with_lang($column)) !== FALSE)
        {
            $i18n = parent::get($translation['column']);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            return $i18n->$translation['language'];
        }

        if (in_array($column, $this->i18n_columns()))
        {
            $i18n = parent::get($column);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            return $i18n;
        }

        return parent::get($column);
    }

    // -------------------------------------------------------------------------

    /**
     * Handles setting of columns
     * Override this method to add custom set behavior
     *
     * @param  string $column Column name
     * @param  mixed  $value  Column value
     * @throws Kohana_Exception
     * @return ORM
     */
    public function set($column, $value)
    {
        if (parent::__isset($column) AND ! in_array($column, $this->i18n_columns()))
        {
            // This helps us prevent nesting issues
            return parent::set($column, $value);
        }

        if (($translation = $this->_column_with_lang($column)) !== FALSE)
        {
            $i18n = parent::get($translation['column']);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            parent::set($translation['column'], $i18n);

            $i18n->$translation['language'] = $value;

            return $this;
        }

        return parent::set($column, $value);
    }

    // -------------------------------------------------------------------------

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values   Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        // Default to expecting everything except the primary key
        if ($expected === NULL)
        {
            $expected = array_keys($this->_table_columns);

            foreach ($this->i18n_columns() as $i18n)
            {
                foreach (self::available_languages() as $lang)
                {
                    $expected[] = $i18n.'_'.$lang;
                }
            }

            // Don't set the primary key by default
            unset($values[$this->_primary_key]);
        }

        return parent::values($values, $expected);
    }

    // -------------------------------------------------------------------------

    /**
     * Updates or Creates the record depending on loaded()
     *
     * @chainable
     * @param  Validation $validation Validation object
     * @return ORM
     */
    public function save(Validation $validation = NULL)
    {
        $i18n_columns = array();

        foreach ($this->i18n_columns() as $i18n)
        {
            if ($this->$i18n instanceof Model_I18n)
            {
                // Save the I18n
                $this->$i18n->save();

                // Remember column
                $i18n_columns[$i18n] = $this->$i18n;

                // Since the I18n model won't return its primary key
                // in __toString we need to do it manually here
                $this->$i18n = $this->$i18n->pk();
            }
        }

        $result = parent::save($validation);

        foreach ($i18n_columns as $key => $i18n)
        {
            $this->$key = $i18n;
        }

        return $result;
    }

    // -------------------------------------------------------------------------

    /**
     * Getter and Setter for current language
     *
     * @return int language id
     */
    public function lang($value = FALSE)
    {
        if ($value === FALSE)
        {
            $value = I18n::$lang;
        }

        // @TODO: add caching
        $lang = Cache::instance()->get('language_id_for_2'.$value, FALSE);

        if ($lang === FALSE)
        {
            $lang = ORM::factory('I18n_Language')->where(is_numeric($value) ? 'id' : 'iso', '=', $value)->find();

            if ( ! $lang->loaded())
            {
                throw new Kohana_Exception('The langugae :langugae does not exist in the database',
                    array(':langugae' => $value));
            }

            $lang = $lang->pk();

            Cache::instance()->set('language_id_for_'.$value, $lang, 100);
        }

        return $lang;
    }

    // -------------------------------------------------------------------------

    /**
     * Searchable columns
     *
     * @return array
     */
    public function i18n_columns()
    {
        return $this->_i18n_columns;
    }

    // -------------------------------------------------------------------------

    /**
     * Editable columns
     *
     * @return array
     */
    public function editable_columns()
    {
        $editable_columns = array_diff(parent::editable_columns(), $this->i18n_columns());

        foreach ($this->i18n_columns() as $i18n)
        {
            foreach (self::available_languages() as $lang)
            {
                $editable_columns[] = $i18n.'_'.$lang;
            }
        }

        return $editable_columns;
    }

    // -------------------------------------------------------------------------

}