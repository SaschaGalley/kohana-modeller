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
            $this->model($this->request->param('model'));
        }
        elseif (is_string($this->_model) AND ! empty($this->_model))
        {
            // Create model from property
            $this->model($this->_model);
        }
        else
        {
            throw new Kohana_Exception('The modeller controller :class needs a model defined.',
                array(':class' => get_class($this)));
        }

        // Check for ID
        $id = $this->request->param('id');

        if ($this->request->post($this->model()->pk()))
        {
            $id = $this->request->post($this->model()->pk());
        }

        if ( ! empty($id))
        {
            $this->model($this->model(), $id);
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
     * Index action
     */
    public function action_index()
    {
        $this->template = $this->_render_list();
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

            foreach ($this->model()->has_many() as $key => $values)
            {
                // connection factory
                $connection = ORM::factory(Inflector::singular($values['model']));

                // load content (list) of connection
                $content = Request::factory($this->route($connection))->query(array($values['foreign_key'] => $this->model()->pk()))->execute()->body();

                // set has many connection
                $connections[$connection->object_name()] = array('title' => ucwords(Inflector::humanize($key)), 'content' => $content);
            }

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

        $view = View::factory('modeller/form/fields/'.$column_type);

        $view->name  = $column;
        $view->value = $this->model()->$column;
        $view->model = $this->model();

        $view->attributes = $this->model()->column_attributes($column);

        return $view;
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

            // make the default get request
            $this->_redirect_to_list();
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Save the entity and redirect to list
     */
    protected function _save_entity(&$model, $values)
    {
        // Set values and save
        $model->values($values)->save();
    }

    // -------------------------------------------------------------------------

    /**
     * Delete action
     */
    public function action_delete()
    {
        if (is_null($this->request->param($this->model()->pk())))
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
            $this->_model = ORM::factory($name, $id);
        }
        elseif ($model instanceof ORM_Modeller)
        {
            if ( ! $model->loaded() AND ! is_null($id))
            {
                $model = ORM::factory($model->object_name(), $id);
            }

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
        $base_route = empty($base_route) ? $this->_base_route : $base_route;
        $base_route = $base_route instanceof ORM_Modeller ? $base_route->controller_name() : $base_route;

        // Return route for model
        return $base_route.'/'.$this->request->param('model');
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