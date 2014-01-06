<?php

$belongings = $model->belongs_to();

$fkModel = ORM::factory($belongings[$name]['model'])->find_all();

$selection = array();
foreach($fkModel as $m)
{
    $selection[$m->id] = (string) $m;
}

echo Form::select($belongings[$name]['foreign_key'], $selection, $value->id, $attributes);