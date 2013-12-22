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
	protected $_icon = '';

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
	protected $_sort_by = '';

	// -------------------------------------------------------------------------

	/**
	 * Icon css class
	 *
	 * @return string
	 */
	public function icon()
	{
		return $this->_icon;
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

	/**
	 *
	 */
	public function getCsv()
	{
		$str = '';
		foreach($this->_editable_columns as $c){
			$str .= '"'.$this->$c.';';
		}

		return $str;
	}

	// -------------------------------------------------------------------------

}