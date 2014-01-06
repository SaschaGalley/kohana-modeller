<?php

$hasones = $this->_model->has_one();

$fkModel = ORM::factory($hasones[$this->_column]['model'])->find_all();

$selection = array();
foreach($fkModel as $m)
{
    $selection[$m->id] = (string) $m;
}

return Form::select($hasones[$this->_column]['foreign_key'], $selection, $this->_model->{$this->_column}->id, $this->_attributes);