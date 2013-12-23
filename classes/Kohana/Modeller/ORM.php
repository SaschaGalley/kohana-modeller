<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CRUD Model
 *
 * @author Sascha Galley
 *
 */
class Kohana_Modeller_ORM extends ORM {

	/**
	 * Permission constants
	 */
	const PERMISSION_CREATE = 1;
	const PERMISSION_READ 	= 2;
	const PERMISSION_UPDATE = 3;
	const PERMISSION_DELETE = 4;

	/**
	 * Icon (css class)
	 * @var string
	 */
	protected $_icon_class = '';

	/**
	 * Editable columns
	 * @var array
	 */
	protected $_i18n_columns = array();

	/**
	 * List columns
	 * @var array
	 */
	protected $_list_columns = array();

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
	protected $_sort_by = '';

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
	 * Checks if object data is set.
	 *
	 * @param  string $column Column name
	 * @return boolean
	 */
	public function __isset($column)
	{
		return (parent::__isset($column) OR in_array($column, $this->_i18n_columns));
	}

	// -------------------------------------------------------------------------

	/**
	 * Handles getting of column
	 * Override this method to add custom get behavior
	 *
	 * @param   string $column Column name
	 * @throws Kohana_Exception
	 * @return mixed
	 */
	public function get($column)
	{
		if (parent::__isset($column))
		{
			return parent::get($column);
		}
		elseif (in_array($column, $this->_i18n_columns))
		{

		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the :class class',
				array(':property' => $column, ':class' => get_class($this)));
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Handles setting of columns
	 * Override this method to add custom set behavior
	 *
	 * @param  string $column Column name
	 * @param  mixed  $value  Column value
	 * @throws Kohana_Exception
	 * @return ORM
	 */
	public function set($column, $value)
	{

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
	public function list_columns()
	{
		return $this->_list_columns;
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
	public function sort_by()
	{
		return $this->_sort_by;
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
	 *
	 */
	public function connections()
	{

	}

	// -------------------------------------------------------------------------

}