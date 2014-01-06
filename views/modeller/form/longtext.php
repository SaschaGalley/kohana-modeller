<div class="wysiwyg-editor-edit" style="float: left;margin:10px 0 10px -60px;" data-editor-state="inactive" data-editor-id="<?php echo $name; ?>">
    <span class="btn btn-mini edit"><i class="icon-edit"></i> Edit</span>
</div>

<div id="<?php echo $name; ?>-div" class="wysiwyg-editor" style="margin:10px 0;width: 500px;"><?php echo $value; ?></div>

<?php echo Form::hidden($name, $value, array('id'=>$name.'-editor')); ?>