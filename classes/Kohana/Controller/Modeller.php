<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Controller_Modeller extends Controller_Template {

    /**
     *
     * @var ORM_Modeller The model name
     */
    protected $_model = '';

    /**
     * @var String The form view
     */
    protected $_form_view = 'modeller/form';

    /**
     * @var String The form view
     */
    protected $_list_view = 'modeller/list';

    /**
     * @var String The base route for modeller
     */
    protected $_base_route = 'modeller';

    // -------------------------------------------------------------------------

    /**
     * Before processing
     */
    public function before()
    {
        parent::before();

        if ( ! is_null($this->request->param('model')))
        {
            // Create model from route param
            $this->model($this->request->param('model'), $this->request->param('id'));
        }
        elseif (is_string($this->_model) AND ! empty($this->_model))
        {
            // Create model from property
            $this->model($this->_model, $this->request->param('id'));
        }
        else
        {
            throw new Kohana_Exception('The modeller controller :class needs a model defined.',
                array(':class' => get_class($this)));
        }

        // A get request may sort or filter the modeller entities
        if ($this->request->method() == HTTP_Request::GET)
        {
            // Sort entities
            $this->model()->sort($this->request->query('sort'));

            // Filter entities
            $this->model()->filter($this->request->query());
        }
    }

    // -------------------------------------------------------------------------

    /**
     * After processing
     */
    public function after()
    {
        // call parent
        parent::after();
    }

    // -------------------------------------------------------------------------

    /**
     * Index action
     */
    public function action_index()
    {
        $this->template = $this->_render_list();
    }

    // -------------------------------------------------------------------------

    /**
     * Add action
     */
    public function action_add()
    {
        $this->template = $this->_render_form();
    }

    // -------------------------------------------------------------------------

    /**
     * Edit action
     */
    public function action_edit()
    {
        if ( ! $this->model()->loaded())
        {
            throw new Kohana_Exception('Something went wrong with loading the model');
        }

        $this->template = $this->_render_form();
    }

    // -------------------------------------------------------------------------

    /**
     * Save action
     */
    public function action_save()
    {
        if ($this->request->method() == HTTP_Request::POST)
        {
            // save entity on post request
            $this->_save_entity($this->_model, $this->request->post());
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Delete action
     */
    public function action_delete()
    {
        if (is_null($this->request->param('id')))
        {
            // request needs id
            throw new Exception('Invalid request. Param "id" expected.');
            return;
        }

        // load model
        $model = ORM::factory($this->_modeller->model()->object_name(), $this->request->param('id'));

        // delete entity
        $model->delete();

        // make the default get request
        $this->_redirect_to_list();
    }

    // -------------------------------------------------------------------------

    /**
     * Getter and Setter for model
     *
     * @param $model    mixed   String or ORM_Modeller
     * @param $id       int     Model ID
     * @return ORM_Modeller
     */
    public function model($model = NULL, $id = NULL)
    {
        if (is_string($model))
        {
            // Make PSR-0 compatible model name
            $name = Inflector::underscore(ucwords(Inflector::humanize($model)));

            // Create model
            $this->_model = ORM_Modeller::factory($name, $id);
        }
        elseif ($model instanceof ORM_Modeller)
        {
            // Set model
            $this->_model = $model;
        }

        // Act as getter
        return $this->_model;
    }

    // -------------------------------------------------------------------------

    /**
     * Return belong to connections for breadcrumb
     */
    public function breadcrumbs($model = NULL)
    {
        if (is_null($model))
        {
            // set this model as default
            $model = $this->_modeller->model();
        }

        $breadcrumbs = array();

        while (count($model->belongs_to()) > 0)
        {
            // get first connection in belongs to
            $belongs_to = key($model->belongs_to());

            // load model
            $model = $model->$belongs_to;

            if ($model->loaded())
            {
                // add model detail to beginning of base modeller breadcrumbs if loaded
                array_unshift($breadcrumbs, array('title' => $model, 'route' => $this->route($model).'/edit/'.$model->pk()));
            }
            // add model overview to beginning of base modeller breadcrumbs
            array_unshift($breadcrumbs, array('title' => $model->humanized_plural(), 'route' => $this->route($model)));
        }

        // add current route
        $breadcrumbs[] = array('title' => $this->_modeller->model()->humanized_plural(), 'route' => $this->route());

        // return breadcrumbs
        return $breadcrumbs;
    }

    // -------------------------------------------------------------------------

    /**
     * Create the route for a specific model
     *
     * @param  ORM  model
     * @return array
     */
    public function route($base_route = FALSE)
    {
        if ( ! empty($base_route))
        {
            $this->_base_route = $base_route;
        }

        if ( ! is_null($this->request->param('model')))
        {
            return $this->_base_route;
        }

        // Return route for model
        return $this->_base_route.'/'.$this->request->param('model');
    }

    // -------------------------------------------------------------------------

    /**
     * Index action
     * @see Seso_Controller_Page::action_index()
     */
    protected function _render_list($query = array(), $search = NULL)
    {
        // list view
        $view = View::factory($this->_list_view);

        // filter list by get request
        $this->model()->filter($query)->sort()->search($search);

        // set list entities
        $view->entity_list = $this->model()->find_all();

        // set the view base route
        $view->route = $this->route();

        // set list headers
        $view->list_headers = $this->_list_headers();

        // Set request query
        $view->query = $query;

        // Set search
        $view->search = $search;

        // Return the view
        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Renders the form
     * if connections is true, a tab for each "has many" connection will be rendered
     */
    protected function _render_form($show_connections = TRUE)
    {
        // form view
        $view = View::factory($this->_form_view);

        // set form of model
        $view->entity = $this->model();

        $view->route = $this->route();

        $view->fields = array();

        // Set form fields
        foreach($this->model()->editable_columns() as $column)
        {
            $view->fields[$column] = $this->_render_form_field($column);
        }

        if ($show_connections AND $this->model()->loaded())
        {
            // generate has many connections
            $connections = array();

            foreach ($model->has_many() as $key => $values)
            {
                // connection factory
                $connection = ORM::factory(Inflector::singular($values['model']));

                // load content (list) of connection
                $content = Request::factory($this->route($connection))->query(array($values['foreign_key'] => $model->id))->execute()->body();

                // set has many connection
                $connections[$connection->object_name()] = array('title' => ucwords(Inflector::humanize($key)), 'content' => $content);
            }

            $view->connections = $connections;
            $view->route = $this->route();

            $view->connections = $connections;
        }

        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Renders a single form field
     */
    protected function _render_form_field($column)
    {
        $column_type = $this->model()->column_type($column);

        if ($column_type == ORM_Modeller::COLUMN_TYPE_HAS_MANY)
        {
            throw new Kohana_Exception('The "has_many" column ´:column´ cannot be editable.');
        }

        $view = View::factory('modeller/form/'.$column_type);

        $view->name  = $column;
        $view->value = $this->model()->$column;
        $view->model = $this->model();

        $view->attributes = $this->model()->column_attributes($column);

        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Save the entity and redirect to list
     */
    protected function _save_entity(&$model, $values)
    {
        // Set values and save
        $model->values($values)->save();

        // make the default get request
        $this->_redirect_to_list();
    }

    // -------------------------------------------------------------------------

    /**
     * Redirect to list page
     */
    protected function _redirect_to_list($message = '')
    {
        $redirect = $this->route();

        if ( ! is_null($this->request->query('redirect_to')))
        {
            // set redirect url from query
            $redirect = $this->request->query('redirect_to');
        }

        if ( ! is_null($this->request->post('redirect_to')))
        {
            // set redirect url from post
            $redirect = $this->request->post('redirect_to');
        }

        // redirect to url
        $this->redirect(BASE_URL.$redirect);
    }

    // -------------------------------------------------------------------------

    /**
     * List headers as array
     */
    protected function _list_headers()
    {
        $headers = array();

        foreach ($this->_model->show_columns() as $column)
        {
            $headers[] = ucwords(Inflector::humanize($column));
        }

        return $headers;
    }

    // -------------------------------------------------------------------------

}