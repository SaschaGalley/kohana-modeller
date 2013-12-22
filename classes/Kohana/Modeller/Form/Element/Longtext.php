<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Modeller_Form_Element_Longtext extends Kohana_Modeller_Form_Element_Element
{
	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_render()
	 */
	protected function _render()
	{
		return '<div class="wysiwyg-editor-edit" style="float: left;margin:10px 0 10px -60px;" data-editor-state="inactive" data-editor-id="'.$this->_column.'">
				<span class="btn btn-mini edit"><i class="icon-edit"></i> Edit</span></div>
				<div id="'.$this->_column.'-div" class="wysiwyg-editor" style="margin:10px 0;width: 500px;">'.$this->_model->{$this->_column}.'</div>'.
				Form::hidden($this->_column, $this->_model->{$this->_column}, array('id'=>$this->_column.'-editor'));
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
