<?php

$hasones = $model->has_one();

$fkModel = ORM::factory($hasones[$name]['model'])->find_all();

$selection = array();
foreach($fkModel as $m)
{
    $selection[$m->id] = (string) $m;
}

return Form::select($hasones[$name]['foreign_key'], $selection, $value->id, $attributes);