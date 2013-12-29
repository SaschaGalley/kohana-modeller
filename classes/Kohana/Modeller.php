<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller modeller
 * @author sg
 *
 */
class Kohana_Modeller {

	const LIST_VIEW_TABLE = 'table';
	const LIST_VIEW_LIST  = 'list';

    /**
     * Add the model name to the route
     * @var string
     */
    protected $_route_add_model = TRUE;

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
	 * Constructor
	 *
	 * @param string Model
	 * @param int ID
	 */
	public function __construct($model, $id = NULL)
	{
		// make PSR-0 compatible model name
		$model = Inflector::underscore(ucwords(Inflector::humanize($model)));

		// create model
		$this->_model = ORM_Modeller::factory($model, $id);
	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the list by request query
	 */
	public function entities($query = array(), $search = FALSE)
	{
		return $entities->find_all();
	}

	// -------------------------------------------------------------------------

	/**
	 * Filter the list by request query
	 */
	public function filter_list($query = array())
	{
		foreach ($this->_model->belongs_to() as $belongs_to)
		{
			if (array_key_exists($belongs_to['foreign_key'], $query))
			{
				$this->_model->where($belongs_to['foreign_key'], '=', $query[$belongs_to['foreign_key']]);
			}
		}

		return $this;
	}

	// -------------------------------------------------------------------------

	/**
	 * Sort the list by model's sort by
	 */
	public function sort_list($order_by = NULL)
	{
		// get model's sort by
		$sort_by = (is_null($order_by)) ? $this->_model->order_by_default() : $order_by;

		if ( ! empty($sort_by))
		{
			// make array
			$sort_by = is_array($sort_by) ? $sort_by : array($sort_by);

			foreach ($sort_by as $sort)
			{
				// order by
				$this->_model->order_by($sort);
			}
		}

		return $this;
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

}