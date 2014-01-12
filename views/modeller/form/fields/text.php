<?php if (Kohana::$config->load('modeller.default.activate_redactor')) : ?>

    <div class="wysiwyg-editor-edit" style="float: left;margin:10px 0 10px -60px;" data-editor-state="inactive" data-editor-id="<?php echo $field->name(); ?>">
        <span class="btn btn-mini edit"><i class="icon-edit"></i> Edit</span>
    </div>

    <div id="<?php echo $field->name(); ?>-div" class="wysiwyg-editor" style="margin:10px 0;width: 500px;"><?php echo $field->value(); ?></div>

    <?php echo Form::hidden($field->name(), $field->value(), array('id' => $field->name().'-editor')); ?>

<?php else : ?>

    <?php echo Form::textarea($field->name(), $field->value(), array('id' => $field->name().'-editor')); ?>

<?php endif; ?>