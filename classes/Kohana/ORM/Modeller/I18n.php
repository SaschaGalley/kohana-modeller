<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CRUD I18n Model
 *
 * @author Sascha Galley
 *
 */
class Kohana_ORM_Modeller_I18n extends ORM_Modeller {

    /**
     * Current language for I18n
     * @var string
     */
    public $lang = 'en';

    /**
     * Current language id for I18n
     * @var string
     */
    protected $_lang_id = FALSE;

    /**
     * I18n columns
     * @var array
     */
    protected $_i18n_columns = array();

    /**
     * I18n data
     * @var array
     */
    protected $_i18n_data = array();

    // -------------------------------------------------------------------------

    /**
     * Checks if object data is set.
     *
     * @param  string $column Column name
     * @return boolean
     */
    public function __isset($column)
    {
        return (parent::__isset($column) OR in_array($column, $this->_i18n_columns));
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
        if (in_array($column, $this->_i18n_columns))
        {
            return ORM::factory('I18n', $this->_object[$column])->translations->where('language_id', '=', $this->lang())->find()->value;
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
        parent::set($column, $value);
    }

    // -------------------------------------------------------------------------

    /**
     *
     */
    public function lang($value = FALSE)
    {
        if ($value === FALSE)
        {
            if ($this->_lang_id !== FALSE)
            {
                return $this->_lang_id;
            }

            $value = I18n::$lang;
        }

        $lang = ORM::factory('I18n_Language')->where(is_numeric($value) ? 'id' : 'iso', '=', $value)->find();

        if ( ! $lang->loaded())
        {
            throw new Kohana_Exception('The langugae :langugae does not exist in the database',
                array(':langugae' => $value));
        }

        $this->_lang_id = $lang->id;

        return $this->_lang_id;
    }

    // -------------------------------------------------------------------------

}