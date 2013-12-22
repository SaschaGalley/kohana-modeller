<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Modeller_Form
{
	/**
	 *
	 * @var Modeller_ORM
	 */
	private static $_default_model = null;

	/**
	 *
	 * @param string $column
	 * @param Modeller_ORM $model
	 * @param array $attributes
	 * @throws Exception
	 * @return Kohana_Modeller_Form_Element
	 */
	public static function create($column, Modeller_ORM $model = null, array $attributes = null)
	{
		if ($model === null) {
			if (self::get_default_model() instanceof Modeller_ORM) {
				$model = self::get_default_model();
			} else {
				throw new Exception("invalid model");
			}
		}


		$columns = $model->list_columns();
		switch (true) {
			case (array_key_exists($column, $model->column_special_types())):
				$special_types = $model->column_special_types();
				if(is_array($special_types[$column])){
					$class = "Modeller_Form_".ucfirst(strtolower($special_types[$column]['type']));
					return new $class($column, $model, $special_types[$column]['attributes']);
				}else{
					$class = "Modeller_Form_".ucfirst(strtolower($special_types[$column]));
					return new $class($column, $model, $attributes);
				}
			// has many connection
			case (array_key_exists($column, $model->has_many())):
				throw new Exception('a "has_many" column cannot be within the editable columns');
			// has one connection
			case (array_key_exists($column, $model->has_one())):
				return new Modeller_Form_HasOne($column, $model, $attributes);
				break;
			// belongs to connection
			case (array_key_exists($column, $model->belongs_to())):
				return new Modeller_Form_BelongsTo($column, $model, $attributes);
				break;
			case ($columns[$column]['data_type'] === 'varchar'):
			case ($columns[$column]['data_type'] === 'date'):
			case ($columns[$column]['data_type'] === 'timestamp'):
			case ($columns[$column]['data_type'] === 'text'):
			case ($columns[$column]['data_type'] === 'longtext'):
			case ($columns[$column]['data_type'] === 'tinyint'):
			case ($columns[$column]['data_type'] === 'int'):
				$class = "Modeller_Form_" . ucfirst(strtolower($columns[$column]['data_type']));
				return new $class($column, $model, $attributes);
			default:
				throw new Exception("unknow modeller_form type");
		}
	}

	/**
	 *
	 * @param Modeller_ORM $model
	 */
	public static function set_default_model(Modeller_ORM $model)
	{
		self::$_default_model = $model;
	}

	/**
	 *
	 * @return Modeller_ORM
	 */
	public static function get_default_model()
	{
		return self::$_default_model;
	}
}
