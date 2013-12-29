<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n_Translation extends ORM {

    /**
     * @var array "Belongs to" connections
     */
    protected $_belongs_to = array(
        'i18n'      => array(),
        'language'  => array('model' => 'I18n_Language'),
    );

    // -------------------------------------------------------------------------

}
