<?php
class Kohana_DynamicDropdown {

	public static function printDynamicDropdown($data, $name)
	{
		$values = explode("{SEPERATE}", $data);

		$html = '<select name="'.$name.'"><option value="-">-</option>';

		foreach($values as $key => $v)
		{
			$html .= '<option value="'.htmlspecialchars($v).'">'.$v.'</option>';
		}
		$html .= '</select>';

		echo $html;
	}
}