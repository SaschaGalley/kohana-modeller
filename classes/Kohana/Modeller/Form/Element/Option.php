<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Modeller_Form_Element_Option extends Kohana_Modeller_Form_Element_Element
{

	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_render()
	 */
	protected function _render()
	{
		return Form::select($this->_column, $this->_attributes['options'], $this->_model->{$this->_column}, array_merge( array(), $this->_attributes));
	}

	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_prepare_save()
	 */
	protected function _prepare_save($value)
	{
		return $value;
	}
}
