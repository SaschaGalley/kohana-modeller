<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n_Language extends ORM_Modeller_I18n {

    /**
     * "Has many" connections
     * @var array
     */
    protected $_has_many = array(
        'translations'   => array('model' => 'I18n_Translation'),
    );

    /**
     * I18n columns
     * @var array
     */
    protected $_i18n_columns = array('name');

    // -------------------------------------------------------------------------

}