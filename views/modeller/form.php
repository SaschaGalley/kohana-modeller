<?php $id = $entity->pk(); ?>

<h1>
    <?php echo (empty($id)) ? __('New '.$entity->humanized_singular()) : __($entity->humanized_singular()).': '.$entity; ?>
</h1>

<form method="post" action="<?php echo $route; ?>/save/" class="form-horizontal">
	<div class="panel panel-default">
	    <div class="panel-heading"><?php echo __('Edit').' '.__(Inflector::humanize(str_replace("Model_","",get_class($entity)))); ?></div>
	    <div class="panel-body">
			<!--  TODO IF WE EDIT A SUB-MODEL THE ACTION URL MUST BE ANOTHER ONE... REDIRECT TO THE CURRENT EDIT PAGE AGAIN...  -->
			    <input type="hidden" name="id" value="<?php echo $entity->pk(); ?>" />
			    <?php echo (isset($parent)) ? '<input type="hidden" name="'.$parent['fk'].'" value="'.$parent['fk_id'].'" />' : '' ?>
				<?php foreach($form->fields() as $field): ?>
					<div class="form-group">
						<label for="<?php echo $field->name(); ?>" class="col-sm-2 control-label"><?php echo $field->label(); ?></label>
						<div class="col-sm-10"><?php echo $field; ?></div>
					</div>
				<?php endforeach; ?>
				<!-- TODO : WE HAVE TO POST THE PARENTS ID IF THIS FORM IS USED TO ADD A SUB-MODEL!! -->

		</div>
		<div class="panel-footer">
			<div class="control-group pull-right">
		    	<div class="controls" style="margin-left:0;">
				    <?php if ($entity->id > 0) :?>
				        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save <?php echo Inflector::humanize(str_replace("Model_","",get_class($entity))); ?></button>
				    <?php else :?>
				        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add <?php echo Inflector::humanize(str_replace("Model_","",get_class($entity))); ?></button>
				    <?php endif; ?>
			    </div>
		    </div>
		    <div class="clearfix"></div>
		</div>
	</div>
</form>

<?php if ( ! empty($id)) : ?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __('Delete').' '.__(Inflector::humanize(str_replace("Model_","",get_class($entity)))); ?></div>
    <div class="panel-body">

		If you delete this entity, you will also loose the following things:
		<ul>
			<?php foreach ($entity->has_many() as $key => $v) : ?>
				<li><i class="<?php echo $entity->$key->icon_class(); ?>"></i> <?php echo $entity->$key->humanized_plural(); ?>: <b><?php echo $entity->$key->count_all(); ?></b></li>
			<?php endforeach; ?>
		</ul>

	</div>
	<div class="panel-footer">
		<div class="control-group pull-right">
			<div class="controls" style="margin-left:0;">
				<form method="post" action="<?php echo $route; ?>/delete/<?php echo $entity->id; ?>">
				    <button type="submit" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
				</form>
		    </div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php endif; ?>

<?php if ( ! empty($id)) : ?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __('Connections'); ?></div>
    <div class="panel-body">
		<ul class="nav nav-tabs">
			<?php $first = TRUE; ?>
			<?php foreach($connections as $key => $connection) :?>
				<li<?php if ($first) echo ' class="active"'; ?>>
					<a href="#pane<?php echo $key; ?>" data-toggle="tab">
						<?php echo $connection['title'] ?> <span class="badge">1</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="tab-content">
			<?php $first = TRUE; ?>
			<?php foreach($connections as $key => $connection) :?>
				<div id="pane<?php echo $key; ?>" class="tab-pane<?php if ($first) echo ' active'; ?>">
					<?php echo $connection['content'] ?>
				</div>
				<?php $first = FALSE; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>