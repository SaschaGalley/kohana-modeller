<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Modeller_Form_Element_Dynamicdropdown extends Kohana_Modeller_Form_Element_Element
{

	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_render()
	 */
	protected function _render()
	{
		$values_orig = $this->_model->{$this->_column};
		$values = explode('{SEPERATE}', $values_orig);

		$html = '<div id="dd_'.$this->_column.'"><div id="dd_'.$this->_column.'_template" class="hidden"><div class="input-append">'.Form::input($this->_column.'[]', '');
		$html .= '<span class="btn add-on delete"><i class="icon-minus"></i></span></div><div class="clear"></div></div><div id="dd_'.$this->_column.'_container">';

		if(strlen($values_orig) > 0){
			foreach($values as $key => $v):
				$html .= '<div class="input-append">';
				$html .= Form::input($this->_column.'[]', $v);
				$html .= '<span class="btn add-on delete"><i class="icon-minus"></i></span></div><div class="clear"></div>';
			endforeach;
		}
		$html .= '</div><span class="btn" id="dd_'.$this->_column.'_add"><i class="icon-plus"></i></span>';

		$html .= '<script type="text/javascript"> $(function(){ initDynamicDropdown("'.$this->_column.'", "'.$this->_attributes['linked_to'].'", "'.$this->_attributes['allow_equal'].'"); }); </script>';
		$html .= '</div>';

		return $html;
	}

	/**
	 *
	 * @see Kohana_Modeller_Form_Element_Element::_prepare_save()
	 */
	protected function _prepare_save($value)
	{
		$ret_value = '';
		foreach($value as $key => $v){
			if($key > 0){
				$ret_value .= $v.'{SEPERATE}';
			}
		}

		// ret
		if(strlen($ret_value) > 0)	return substr($ret_value, 0, -10);
		return '';
	}
}
