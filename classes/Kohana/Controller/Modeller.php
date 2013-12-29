<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Controller_Modeller extends Controller_Template {

    /**
     * The modeller object
     * @var Modeller
     */
    protected $_modeller;

    /**
     * The model name
     * @var string
     */
    protected $_model_name = '';

    /**
     * The form view
     * @var string
     */
    protected $_form_view = 'modeller/form';

    /**
     * The form view
     * @var string
     */
    protected $_list_view = 'modeller/list';

    // -------------------------------------------------------------------------

    /**
     * Before processing
     */
    public function before()
    {
        parent::before();

        // create model by request params
        $this->_modeller = Modeller::factory($this->_model_name, $this->request->param('id'));

        // set the modeller base route
        $this->_modeller->base_route(Request::current()->uri().URL::query());
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
     * @see Seso_Controller_Page::action_index()
     */
    public function action_index()
    {
        if ($this->request->method() == HTTP_Request::GET)
        {
            // list view
            $view = View::factory('modeller/list');

            // filter list by get request
            $this->_modeller->filter_list($query)->sort_list()->search($search);

            // set list entities
            $view->entity_list = $this->_modeller->model()->find_all();

            // set the view base route
            $view->route = $this->_base_route.'/'.str_replace("model_", "", strtolower(get_class($this->_model)));

            // set list headers
            $view->list_headers = $this->_list_headers();

            // add request query for referall magic stuff!
            $view->query = $query;

            return $view;
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Add action
     */
    public function action_add()
    {
        if ($this->request->method() == HTTP_Request::POST)
        {
            // save entity on post request
            $this->_save_entity($this->_model);
        }

        // form view
        $view = View::factory('modeller/form');

        // set form of model
        $view->entity = $this->_modeller->model();

        $view->route = $this->route();

        // set form of model
        $view->form_view = View::factory( 'modeller/form', array('entity' => $this->_modeller->model()));

        // set view inside main area
        $this->template->areas('main', $view);
    }

    // -------------------------------------------------------------------------

    /**
     * Edit action
     */
    public function action_edit()
    {
        if (is_null($this->request->param('id')))
        {
            // edit request needs a id
            throw new Exception('Invalid request. Param "id" expected.');
            return;
        }

        // load model with id
        $model = ORM::factory($this->_modeller->model()->object_name(), $this->request->param('id'));

        if ($this->request->method() == HTTP_Request::POST)
        {
            // save entity on post request
            $this->_save_entity($this->model);
        }

        // form view
        $view = View::factory('modeller/form');

        // set form of model
        $view->entity = $model;

        // set form of model
        $view->form_view = View::factory('modeller/form', array('entity' => $model));

        // set view inside dashboard area
        $this->template->areas('main', $view);


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

        $this->template->areas('main')->connections =  $connections;
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
     * Return belong to connections for breadcrumb
     */
    public function base_breadcrumbs($model = NULL)
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
    public function route($model = NULL)
    {
        if (is_null($model))
        {
            // set this model as default
            $model = $this->_modeller->model();
        }

        $parts = explode('_', $model->object_name());

        foreach ($parts as &$part)
        {
            $part = Inflector::singular($part);
        }

        // return route for model
        return $this->_route_add_model ? $this->_modeller->base_route.'/'.implode('_', $parts) : $this->_modeller->base_route;
    }

    // -------------------------------------------------------------------------

    /**
     * Index action
     * @see Seso_Controller_Page::action_index()
     */
    protected function _render_list($query = array(), $search = NULL)
    {
        // list view
        $view = View::factory('modeller/list');

        // filter list by get request
        $this->_modeller->filter_list($query)->sort_list();

        // set list entities
        $view->entity_list = is_null($search) ? $this->_modeller->model->find_all() : $this->_search($search);

        // set the view base route
        $view->route = $this->_base_route.'/'.str_replace("model_", "", strtolower(get_class($this->_model)));

        // set list headers
        $view->list_headers = $this->_list_headers();

        // add request query for referall magic stuff!
        $view->query = $query;

        // Return the view
        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Renders the form
     * if connections is true, a tab for each "has many" connection will be rendered
     */
    protected function _render_form($connections = TRUE)
    {

    }

    // -------------------------------------------------------------------------

    /**
     * Save the entity and redirect to list
     */
    protected function _save_entity(&$model)
    {
        // set values
        $model->values($this->request->post());

        // save entity
        $model->save();

        // make the default get request
        $this->_redirect_to_list();
    }

    // -------------------------------------------------------------------------

    /**
     * Redirect to list page
     */
    protected function _redirect_to_list($message='')
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

}