<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n_Translation extends ORM {

    /**
     * "Belongs to" connections
     * @var array
     */
    protected $_belongs_to = array(
        'language'      => array(),
        'translation'   => array(),
    );


    // -------------------------------------------------------------------------

}
