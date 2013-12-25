<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n extends ORM {

    /**
     * Table name
     * @var string
     */
    protected $_table_name = 'I18n';

    /**
     * "Belongs to" connections
     * @var array
     */
    protected $_belongs_to = array(
        'language'      => array(),
        'translation'   => array(),
    );

    /**
     * "Has many" connections
     * @var array
     */
    protected $_has_many = array(
        'translations'   => array('model' => 'I18n_Translation'),
    );


    // -------------------------------------------------------------------------

}
