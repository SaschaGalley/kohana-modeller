<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Modeller_Form {

    // -------------------------------------------------------------------------
    //
    public static function factory(ORM_Modeller $model)
    {
        return new Modeller_Form($model);
    }

    // -------------------------------------------------------------------------

    public static function render(ORM_Modeller $model, View $view)
    {
        $view->form = Modeller_Form::factory($model);
    }

    // -------------------------------------------------------------------------

    protected $_model = NULL;

    protected $_fields = array();

    protected $_connections = array();

    // -------------------------------------------------------------------------

    public function __construct(ORM_Modeller $model)
    {
        $this->_model = $model;

        foreach ($this->_model->editable_columns() as $column)
        {
            $this->_fields[$column] = Modeller_Form_Field::factory($model, $column);//$this->_render_form_field($column);
        }
    }

    // -------------------------------------------------------------------------

    public function __toString()
    {
        $view = View::factory($this->_form_view);
    }

    // -------------------------------------------------------------------------

    /**
     * Renders a single form field
     */
    protected function _render_form_field($column)
    {
        $column_type = $this->model()->column_type($column);

        $view = View::factory('modeller/form/fields/'.$column_type);

        $view->name  = $column;
        $view->value = $this->model()->$column;
        $view->model = $this->model();

        return $view;
    }

    // -------------------------------------------------------------------------

    public function model()
    {
        return $this->_model;
    }

    // -------------------------------------------------------------------------

    public function fields()
    {
        return $this->_fields;
    }

    // -------------------------------------------------------------------------

    public function connections()
    {
        return $this->_connections;
    }

    // -------------------------------------------------------------------------

}