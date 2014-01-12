<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Modeller Controller
 * @author sg
 *
 */
class Kohana_Modeller {

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

    // -------------------------------------------------------------------------

    /**
     * Modeller factory
     */
    public static function factory($model, $id = NULL)
    {
        return new Modeller($model, $id);
    }

    // -------------------------------------------------------------------------

    /**
     *
     */
    public function __construct($model, $id = NULL)
    {
        // make PSR-0 compatible model name
        $model = ucwords(implode('_', array_map(array('Inflector', 'singular'), explode('_', $model))));

        // create model
        $this->_model = ORM_Modeller::factory($model, $id);
    }

    // -------------------------------------------------------------------------

    /**
     * Index action
     * @see Seso_Controller_Page::action_index()
     */
    public function render_list(Request $request)
    {
        // list view
        $view = View::factory($this->_list_view);

        // filter list by get request
        $this->model()->filter($request->query())->sort($request->query('sort'))->search($request->query('search'));

        // set list entities
        $view->entities = $this->model()->find_all();

        // set the view base route
        $view->route = $this->route();

        // set list headers
        $view->list_headers = $this->_list_headers();

        // Set request query
        $view->filters = $request->query();

        // Set search
        $view->search = $request->query('search');

        // Set sort
        $view->sort = $request->query('sort');

        $view->model = $this->model();

        $view->header = TRUE;
        $view->buttons = TRUE;

        // Return the view
        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Renders the form
     * if connections is true, a tab for each "has many" connection will be rendered
     */
    public function render_form(Request $request, $show_connections = TRUE)
    {
        // form view
        $view = View::factory($this->_form_view);

        // set form of model
        $view->entity = $this->model();

        $view->route = $this->route();

        $view->form = Modeller_Form::factory($this->model());

        if ($show_connections AND $this->model()->loaded())
        {
            // generate has many connections
            $connections = array();

            foreach ($this->model()->show_connections(TRUE) as $key => $values)
            {
                // connection factory
                $connection = Modeller::factory($values['model']);

                //$route = str_replace(BASE_URL, '', $this->route($connection));

                $request = Request::factory()->query($values['foreign_key'], $this->model()->pk());

                // load content (list) of connection
                $content = $connection->render_list($request);

                $content->header = FALSE;

                // set has many connection
                $connections[$connection->model()->object_name()] = array('title' => ucwords(Inflector::humanize($key)), 'content' => $content);
            }

            $view->connections = $connections;
        }

        return $view;
    }

    // -------------------------------------------------------------------------

    /**
     * Save the entity and redirect to list
     */
    public function save($values)
    {
        // Set values and save
        $this->model()->values($values)->save();
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
            $name = ucwords(implode('_', array_map(array('Inflector', 'singular'), explode('_', $model))));

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
            $model = $this->model();
        }

        $breadcrumbs = array();

        while (count($model->belongs_to()) > 0)
        {
            // get first connection in belongs to
            $belongs_to = key($model->belongs_to());

            var_dump($model->belongs_to());

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
        $breadcrumbs[] = array('title' => $this->model()->humanized_plural(), 'route' => $this->route());

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