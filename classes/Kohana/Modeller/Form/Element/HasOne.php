<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Modeller_Form_Element_HasOne extends Kohana_Modeller_Form_Element_Element
{
	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_render()
	 */
	protected function _render()
	{
		$hasones = $this->_model->has_one();

		$fkModel = ORM::factory($hasones[$this->_column]['model'])->find_all();

		$selection = array();
		foreach($fkModel as $m)
		{
			$selection[$m->id] = (string) $m;
		}

		return Form::select($hasones[$this->_column]['foreign_key'], $selection, $this->_model->{$this->_column}->id, $this->_attributes);
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
