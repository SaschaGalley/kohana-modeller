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
    protected $_modeller = NULL;

    /**
     * @var String The form view
     */
    protected $_form_view = 'modeller/form';

    /**
     * @var String The form view
     */
    protected $_list_view = 'modeller/list';

    // -------------------------------------------------------------------------

    /**
     * Before processing
     */
    public function before()
    {
        parent::before();

        $model = Arr::first_nonempty(array(
            $this->request->param('model'),
            $this->_modeller,
        ));

        $this->_modeller = Modeller::factory($model);

        $id = Arr::first_nonempty(array(
            $this->request->param('id'),
            $this->request->post($this->_modeller->model()->primary_key()),
        ));

        $this->_modeller->model($model, $id);
    }

    // -------------------------------------------------------------------------

    /**
     * Index action
     */
    public function action_index()
    {
        if ($this->request->method() !== HTTP_Request::GET)
        {
            throw new Kohana_Exception('Wrong request method. GET is required for index action.');
        }

        $this->template = $this->_modeller->render_list($this->request);
    }

    // -------------------------------------------------------------------------

    /**
     * Add action
     */
    public function action_add()
    {
        $this->template = $this->_modeller->render_form($this->request);
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

        $this->template = $this->_modeller->render_form($this->request);
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
            $this->_modeller->save($this->request->post());

            // make the default get request
            $this->_redirect_to_list();
        }
    }

    // -------------------------------------------------------------------------

    /**
     * Delete action
     */
    public function action_delete()
    {
        $id = $this->request->param($this->model()->primary_key());

        if (is_null($id))
        {
            // request needs id
            throw new Exception('Invalid request. Param "id" expected.');
            return;
        }

        // load model
        $model = ORM::factory($this->_modeller->model()->object_name(), $id);

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
        // Act as getter
        return $this->_modeller->model($model, $id);
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
        $this->redirect($redirect);
    }

    // -------------------------------------------------------------------------

    /**
     * Create the route for a specific model
     *
     * @param  ORM  model
     * @return array
     */
    public function route($model = FALSE)
    {
        $base_route = empty($model) ? $this->model() : $model;
        $base_route = $base_route instanceof ORM_Modeller ? $base_route->controller_route() : $base_route;
        //$base_route .= ($model = $this->request->param('model', FALSE)) ? '/'.$model.'/' : '/';

        // Return route for model
        return $base_route;
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

            if (isset($model->$belongs_to))
            {
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
        }

        // add current route
        $breadcrumbs[] = array('title' => $this->_modeller->model()->humanized_plural(), 'route' => $this->route());

        // return breadcrumbs
        return $breadcrumbs;
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