<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Modeller_Form_Field {

    protected $_model = '';

    protected $_column = '';

    // -------------------------------------------------------------------------
    //
    public static function factory(ORM_Modeller $model, $column)
    {
        return new Modeller_Form_Field($model, $column);
    }

    // -------------------------------------------------------------------------

    public function __construct(ORM_Modeller $model, $column)
    {
        $this->_model = $model;
        $this->_column = $column;
    }

    // -------------------------------------------------------------------------

    public function __toString()
    {
        $column_type = $this->_model->column_type($this->_column);

        $view = View::factory('modeller/form/fields/'.$column_type);

        $view->field = $this;

        return (string) $view;
    }

    // -------------------------------------------------------------------------

    public function model()
    {
        return $this->_model;
    }

    // -------------------------------------------------------------------------

    public function value()
    {
        $column = $this->_column;
        return $this->_model->$column;
    }

    // -------------------------------------------------------------------------

    public function name()
    {
        return $this->_column;
    }

    // -------------------------------------------------------------------------

    public function label()
    {
        $label = __(ucwords(Inflector::humanize($this->_column)));

        if ($splitted = $this->_model->split_i18n_column($this->_column))
        {
            $label = __(ucwords($splitted['column'])).' '.Modeller_I18n::language($splitted['language']);
        }

        return $label;
    }

    // -------------------------------------------------------------------------

    public function attributes()
    {
        return array_merge(array('class' => 'form-control'), $this->_model->column_attributes($this->_column));
    }

    // -------------------------------------------------------------------------

}