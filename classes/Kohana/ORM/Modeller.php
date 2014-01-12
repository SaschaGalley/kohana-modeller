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
	 * Column types
	 * The value of the constant is used to find the according view
	 */
	const COLUMN_TYPE_HAS_ONE 		= 'has_one';
	const COLUMN_TYPE_HAS_MANY 		= 'has_many';
	const COLUMN_TYPE_BELONGS_TO 	= 'belongs_to';
	const COLUMN_TYPE_BINARY 		= 'binary';
	const COLUMN_TYPE_DATE 			= 'date';
	const COLUMN_TYPE_FLOAT 		= 'float';
	const COLUMN_TYPE_IMAGE 		= 'image';
	const COLUMN_TYPE_INT 			= 'int';
	const COLUMN_TYPE_LONGTEXT 		= 'longtext';
	const COLUMN_TYPE_SELECT 		= 'select';
	const COLUMN_TYPE_TEXT 			= 'text';
	const COLUMN_TYPE_TIMESTAMP 	= 'timestamp';
	const COLUMN_TYPE_TINYINT	 	= 'tinyint';
	const COLUMN_TYPE_VARCHAR 		= 'varchar';

	/**
	 * @var Array Specific colum types
	 */
	protected $_column_types = array();

	/**
	 * @var Array Specific colum attributes
	 */
	protected $_column_attributes = array();

	/**
	 * @var Mixed Default order by, string or array
	 */
	protected $_order_by_default = FALSE;

	/**
	 * @var Array The columns that should be shown in the list view
	 */
	protected $_show_columns = array();

	/**
	 * @var Array The has many connections that should be shown as connection tab
	 */
	protected $_show_connections = array();

	/**
	 * @var Array Editable columns
	 */
	protected $_editable_columns = array();

	/**
	 *  @var Array Searchable columns
	 */
	protected $_searchable_columns = array();

	/**
	 *  @var Array Sortable columns
	 */
	protected $_sortable_columns = array();

	/**
	 * @var String Icon (css class)
	 */
	protected $_icon_class = '';

	/**
	 * @var String The base route to its modeller controller
	 */
	protected $_controller_route = 'modeller';

	// -------------------------------------------------------------------------

	/**
	 * Search functionality
	 *
	 * @param  String search for
	 * @return ORM_Modeller this
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

			$this->where_close();
		}

		return $this;
	}

	// -------------------------------------------------------------------------

	/**
	 * Filter functionality
	 */
	public function filter($query = array(), $value = FALSE)
	{
		if (empty($query))
		{
			return $this;
		}

		if (is_string($query) AND $value !== FALSE)
		{
			$query = array($query => $value);
		}

		$belongs_to = $this->belongs_to();

		foreach ($this->list_columns() as $column => $properties)
		{
			if (array_key_exists($column, $query))
			{
				$this->where($column, '=', $query[$column]);
			}
		}

		foreach ($this->belongs_to() as $belongs_to)
		{
			if (array_key_exists($belongs_to['foreign_key'], $query))
			{
				$this->where($belongs_to['foreign_key'], '=', $query[$belongs_to['foreign_key']]);
			}
		}

		return $this;
	}

	// -------------------------------------------------------------------------

	/**
	 * Filter functionality
	 */
	public function sort($order_by = FALSE)
	{
		// Get order by
		$sort_by = (is_null($order_by)) ? $this->order_by_default() : $order_by;

		if ( ! empty($sort_by))
		{
			// Make array
			$sort_by = is_array($sort_by) ? $sort_by : array($sort_by);

			foreach ($sort_by as $sort)
			{
				// order by
				$this->order_by($sort);
			}
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
		return ucwords(Inflector::humanize(Inflector::singular($this->object_name())));
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
	public function controller_route()
	{
		$route = Route::get($this->_controller_route);

		$controller_name = implode('_', array_map(array('Inflector', 'plural'), explode('_', $this->object_name())));
		$model_name = implode('_', array_map(array('Inflector', 'singular'), explode('_', $this->object_name())));

		return Route::url(Route::name($route), array(
			'controller' => $this->_controller_route == 'modeller' ? 'modeller' : $controller_name,
			'model' => $model_name,
		));
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
	 * Get the column type
	 *
	 * @return string
	 */
	public function column_type($column)
	{
		if (array_key_exists($column, $this->has_many()))
		{
			return ORM_Modeller::COLUMN_TYPE_HAS_MANY;
		}

		if (array_key_exists($column, $this->belongs_to()))
		{
			return ORM_Modeller::COLUMN_TYPE_BELONGS_TO;
		}

		if ($this instanceof ORM_Modeller_I18n AND (in_array($column, $this->i18n_columns()) OR $this->split_i18n_column($column)))
		{
			return ORM_Modeller::COLUMN_TYPE_VARCHAR;
		}

		if (array_key_exists($column, $this->has_one()))
		{
			return ORM_Modeller::COLUMN_TYPE_HAS_ONE;
		}

		if (array_key_exists($column, $this->_column_types))
        {
            return (is_array($this->_column_types[$column])) ? $this->_column_types[$column]['type'] : $this->_column_types[$column];
        }

        $columns = $this->list_columns();

        switch (strtolower($columns[$column]['data_type']))
        {
        	case ('varchar'):
        		return ORM_Modeller::COLUMN_TYPE_VARCHAR;
			case ('date'):
				return ORM_Modeller::COLUMN_TYPE_DATE;
			case ('timestamp'):
				return ORM_Modeller::COLUMN_TYPE_TIMESTAMP;
			case ('text'):
				return ORM_Modeller::COLUMN_TYPE_TEXT;
			case ('longtext'):
				return ORM_Modeller::COLUMN_TYPE_LONGTEXT;
			case ('tinyint'):
				return ORM_Modeller::COLUMN_TYPE_TINYINT;
			case ('int'):
				return ORM_Modeller::COLUMN_TYPE_INT;
			default:
				return ORM_Modeller::COLUMN_TYPE_VARCHAR;
        }

        return FALSE;
	}

	// -------------------------------------------------------------------------

	/**
	 * Column attributes
	 *
	 * @return array
	 */
	public function column_attributes($column)
	{
		return (array_key_exists($column, $this->_column_attributes)) ? $this->_column_attributes[$column] : array();
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
	 * Columns to show in list view
	 *
	 * @return array
	 */
	protected function _get_info($array = FALSE, array $info, $full_info = FALSE, $info_as_default = TRUE)
	{
		if ($array === FALSE)
		{
			return array();
		}

		if (empty($array) AND $info_as_default === TRUE)
		{
			$array = array_keys($info);
		}

		if ($full_info === TRUE)
		{
			$array = array_intersect_key($info, array_flip($array));
		}

		return $array;
	}

	// -------------------------------------------------------------------------

	/**
	 * Columns to show in list view
	 *
	 * @return array
	 */
	public function show_columns($full_info = FALSE)
	{
		$all_columns = $this->list_columns();
		unset($all_columns[$this->_primary_key]);

		return $this->_get_info($this->_show_columns, $all_columns, $full_info);
	}

	// -------------------------------------------------------------------------

	/**
	 * Columns to show in list view
	 *
	 * @return array
	 */
	public function show_connections($full_info = FALSE)
	{
		return $this->_get_info($this->_show_connections, $this->has_many(), $full_info);
	}

	// -------------------------------------------------------------------------

	/**
	 * Editable columns
	 *
	 * @return array
	 */
	public function editable_columns($full_info = FALSE)
	{
		$all_columns = $this->list_columns();
		unset($all_columns[$this->_primary_key]);

		return $this->_get_info($this->_editable_columns, $all_columns, $full_info);
	}

	// -------------------------------------------------------------------------

	/**
	 * Searchable columns
	 *
	 * @return array
	 */
	public function searchable_columns($full_info = FALSE)
	{
		return $this->_get_info($this->_searchable_columns, $this->list_columns(), $full_info);
	}

	// -------------------------------------------------------------------------

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function sortable_columns($full_info = FALSE)
	{
		return $this->_get_info($this->_sortable_columns, $this->list_columns(), $full_info);
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

}