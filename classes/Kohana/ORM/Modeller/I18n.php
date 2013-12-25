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
    public $lang = 'en-us';

    /**
     * Editable columns
     * @var array
     */
    protected $_i18n_columns = array();

    // -------------------------------------------------------------------------

    /**
     * Prepares the model database connection, determines the table name,
     * and loads column information.
     *
     * @return void
     */
    protected function _initialize()
    {
        $this->lang = I18n::$lang;

        foreach ($this->_i18n_columns as $column)
        {
            $this->_belongs_to[$column] = array('model' => 'I18n');
        }

        parent::_initialize();
    }

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

        }
        elseif (parent::__isset($column))
        {
            return parent::get($column);
        }
        else
        {
            throw new Kohana_Exception('The :property property does not exist in the :class class',
                array(':property' => $column, ':class' => get_class($this)));
        }
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

    }

    // -------------------------------------------------------------------------

}