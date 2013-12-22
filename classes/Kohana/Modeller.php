<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller modeller
 * @author sg
 *
 */
class Kohana_Modeller {

	// -------------------------------------------------------------------------

	/**
	 * Modeller factory
	 */
	public static function factory($model, $id = NULL)
	{
		return new Kohana_Modeller($model, $id);
	}

	// -------------------------------------------------------------------------

	/**
	 * The modeller object
	 * @var Modeller
	 */
	protected $_model;

	/**
	 * The base route for modeller
	 * @var string
	 */
	protected $_base_route = 'modeller';

	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct($model, $id = NULL)
	{
		// make PSR-0 compatible model name
		$model = Inflector::underscore(ucwords(Inflector::humanize($model)));

		// create model
		$this->_model = Modeller_ORM::factory($model, $id);
	}

	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function render_list($query = array(), $search = null)
	{
		// list view
		$view = View::factory('modeller/list');

		// filter list by get request
		$this->_filter_list($query);

		// order the list
		$this->_sort_list();

		// set list entities
		$view->entity_list = is_null($search) ? $this->_model->find_all() : $this->_search($search);

		// set the view base route
		$view->route = $this->_base_route.'/'.str_replace("model_","",strtolower(get_class($this->_model)));

		// set list headers
		$view->list_headers = $this->_list_headers();

		// add request query for referall magic stuff!
		$view->query = $query;

		return $view;
	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the list by request query
	 */
	protected function _filter_list($query = array())
	{
		foreach ($this->_model->belongs_to() as $belongs_to)
		{
			if (array_key_exists($belongs_to['foreign_key'], $query))
			{
				$this->_model->where($belongs_to['foreign_key'], '=', $query[$belongs_to['foreign_key']]);
			}
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Sort the list by model's sort by
	 */
	protected function _sort_list()
	{
		// get model's sort by
		$sort_by = $this->_model->sort_by();

		// make array
		$sort_by = is_array($sort_by) ? $sort_by : array($sort_by);

		foreach ($sort_by as $sort)
		{
			// order by
			$this->_model->order_by($sort);
		}
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

	/**
	 * Search for param
	 */
	public function search($query)
	{
		if (sizeof($this->_model->searchable_columns()) > 0)
		{
			$this->_model->where_open();

			foreach ($this->_model->searchable_columns() as $column)
			{
				$this->_model->or_where($column, 'LIKE', '%'.$query.'%');
			}

			return $this->_model->where_close()->find_all();
		}
		else
		{
			return false;
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Set and get base route
	 *
	 * @param  string  route
	 * @return string
	 */
	public function base_route($route = NULL)
	{
		if ( ! is_null($route))
		{
			// act as setter
			$this->_base_route = $route;
		}

		// act as getter
		return $this->_base_route;
	}

	// -------------------------------------------------------------------------

	/**
	 * Getter for model
	 *
	 * @return Modeller_ORM
	 */
	public function model()
	{
		// act as getter
		return $this->_model;
	}

	// -------------------------------------------------------------------------

}