<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n_Language extends ORM_Modeller_I18n {

    /**
     * "Has many" connections
     * @var array
     */
    protected $_has_many = array(
        'translations'   => array('model' => 'I18n_Translation', 'foreign_key' => 'language_id'),
    );

    /**
     * Default order by, string or array
     * @var mixed
     */
    protected $_order_by_default = 'name';

    /**
     * List columns
     * @var array
     */
    protected $_show_columns = array('name', 'iso');

    /**
     * Editable columns
     * @var array
     */
    protected $_editable_columns = array('name', 'iso');

    /**
     * Searchable columns
     * @var array
     */
    protected $_searchable_columns = array('name', 'iso');

    /**
     * I18n columns
     * @var array
     */
    protected $_i18n_columns = array('name');

    // -------------------------------------------------------------------------

    public function __toString()
    {
        return (string) $this->name;
    }

    // -------------------------------------------------------------------------

}