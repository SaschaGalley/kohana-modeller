<?php

$belongings = $field->model()->belongs_to();

$fkModel = ORM::factory($belongings[$field->name()]['model'])->find_all();

$selection = array();
foreach($fkModel as $m)
{
    $selection[$m->id] = (string) $m;
}

echo Form::select($belongings[$field->name()]['foreign_key'], $selection, $field->value()->id, $field->attributes());