<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CRUD Model
 *
 * @author Sascha Galley
 *
 */
class Kohana_ORM_Modeller extends ORM {

	/**
	 * Permission constants
	 */
	const PERMISSION_CREATE = 1;
	const PERMISSION_READ 	= 2;
	const PERMISSION_UPDATE = 3;
	const PERMISSION_DELETE = 4;

	/**
	 * List columns
	 * @var array
	 */
	protected $_show_columns = array();

	/**
	 * Editable columns
	 * @var array
	 */
	protected $_editable_columns = array();

	/**
	 * Searchable columns
	 * @var array
	 */
	protected $_searchable_columns = array();

	/**
	 * specific colum types
	 * @var array
	 */
	protected $_column_special_types = array();

	/**
	 * Default order by, string or array
	 * @var mixed
	 */
	protected $_order_by_default = FALSE;

	/**
	 * Enable filtering in list, string or array
	 * @var mixed
	 */
	protected $_filter_by = FALSE;

	/**
	 * Icon (css class)
	 * @var string
	 */
	protected $_icon_class = '';

	// -------------------------------------------------------------------------

	/**
	 * Search functionality
	 */
	public function search($query = NULL)
	{
		if ( ! empty($query) AND sizeof($this->searchable_columns()) > 0)
		{
			$this->where_open();

			foreach ($this->searchable_columns() as $column)
			{
				$this->or_where($column, 'LIKE', '%'.$query.'%');
			}

			$this->where_close()->find_all();
		}

		return $this;
	}

	// -------------------------------------------------------------------------

	/**
	 * The human readable singular object name
	 *
	 * @return string
	 */
	public function humanized_singular()
	{
		return ucwords(Inflector::humanize($this->object_name()));
	}

	// -------------------------------------------------------------------------

	/**
	 * The human readable plural object name
	 *
	 * @return string
	 */
	public function humanized_plural()
	{
		return Inflector::plural($this->humanized_singular());
	}

	// -------------------------------------------------------------------------

	/**
	 * The human readable plural object name
	 *
	 * @return string
	 */
	public function controller_name()
	{
		return implode('_', array_map(array('Inflector', 'plural'), explode('_', $this->object_name())));
	}

	// -------------------------------------------------------------------------

	/**
	 * Privileges
	 *
	 * @return string
	 */
	public function privileges()
	{

	}

	// -------------------------------------------------------------------------

	/**
	 * Columns to show in list view
	 *
	 * @return array
	 */
	public function show_columns()
	{
		return $this->_show_columns;
	}

	// -------------------------------------------------------------------------

	/**
	 * Editable columns
	 *
	 * @return array
	 */
	public function editable_columns()
	{
		return $this->_editable_columns;
	}

	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function order_by_default()
	{
		return $this->_order_by_default;
	}

	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function filter_by()
	{
		return $this->_filter_by;
	}

	// -------------------------------------------------------------------------

	/**
	 * Searchable columns
	 *
	 * @return array
	 */
	public function searchable_columns()
	{
		return $this->_searchable_columns;
	}

	// -------------------------------------------------------------------------

	/**
	 * specific column types
	 *
	 * @return array
	 */
	public function column_special_types()
	{
		return $this->_column_special_types;
	}

	// -------------------------------------------------------------------------

	/**
	 * Icon css class
	 *
	 * @return string
	 */
	public function icon_class()
	{
		return $this->_icon_class;
	}

	// -------------------------------------------------------------------------

	/**
	 *
	 */
	public function connections()
	{

	}

	// -------------------------------------------------------------------------

}