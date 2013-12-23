<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Modeller_Form_Element
{
	/**
	 * @var Modeller_ORM
	 */
	protected $_model = NULL;

	/**
	 * @var string
	 */
	protected $_column;

	/**
	 * @var array
	 */
	protected $_attributes = NULL;

	// -------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param string $column
	 * @param Modeller_ORM $model
	 * @param array $attributes
	 */
	public function __construct($column, Modeller_ORM $model, array $attributes = NULL)
	{
		if (is_null($attributes))
		{
			$attributes = array();
		}

		$this->_attributes = $attributes;
		$this->_column = $column;
		$this->_model = $model;
	}

	// -------------------------------------------------------------------------

	/**
	 * renders the modeller form for the specific (editable) column
	 */
	abstract protected function _render();

	// -------------------------------------------------------------------------

	/**
	 * prepares the value for saving
	 *
	 * @param mixed $value
	 */
	abstract protected function _prepare_save($value);

	// -------------------------------------------------------------------------

	/**
	 * prepares the value for saving
	 *
	 * @param mixed $value
	 */
	public function prepare_save($value)
	{
		return $this->_prepare_save($value);
	}

	// -------------------------------------------------------------------------

	/**
	 * renders the modeller form for the specific (editable) column
	 */
	public function render()
	{
		return $this->_render();
	}

	// -------------------------------------------------------------------------

	/**
	 * __toString method => alias of render
	 *
	 * @return string
	 * @uses Modeller_Form_Element
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			/**
			 * Display the exception message.
			 *
			 * We use this method here because it's impossible to throw an
			 * exception from __toString().
			 */
			$error_response = Kohana_exception::_handler($e);

			return $error_response->body();
		}
	}

	// -------------------------------------------------------------------------
}