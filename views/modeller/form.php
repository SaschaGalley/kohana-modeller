<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#paneEdit" data-toggle="tab"><?php echo (Inflector::humanize(str_replace("Model_","",get_class($entity))) != "") ? Inflector::humanize(str_replace("Model_","",get_class($entity))) : 'Add';?></a></li>
		<?php if ($entity->id > 0) : ?>
			<?php foreach($connections as $key => $connection) :?>
				<li><a href="#pane<?php echo $key; ?>" data-toggle="tab"><?php echo $connection['title'] ?></a></li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div id="paneEdit" class="tab-pane active">
		<!--  TODO IF WE EDIT A SUB-MODEL THE ACTION URL MUST BE ANOTHER ONE... REDIRECT TO THE CURRENT EDIT PAGE AGAIN...  -->
		<form method="post" action="<?php echo BASE_URL.$route; ?>save/" class="form-horizontal">
		    <input type="hidden" name="id" value="<?php echo $entity->id; ?>" />
		    <?php echo (isset($parent)) ? '<input type="hidden" name="'.$parent['fk'].'" value="'.$parent['fk_id'].'" />' : '' ?>
		    <table class="table table-condensed">
				<thead>
					<tr>
						<th>Field</th><th>Value</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($fields as $name => $field): ?>
					<tr>
						<td><?php echo ucwords(Inflector::humanize($name)); ?></td>
						<td><?php echo $field; ?></td>
					</tr>
				<?php endforeach; ?>
				<!-- TODO : WE HAVE TO POST THE PARENTS ID IF THIS FORM IS USED TO ADD A SUB-MODEL!! -->
				</tbody>
		    </table>
		    <br />
		    <div class="control-group">
		    	<div class="controls" style="margin-left:0;">
				    <?php if ($entity->id > 0) :?>
				        <button type="submit" class="btn btn-primary"><i class="icon-edit icon-white"></i> Save <?php echo Inflector::humanize(str_replace("Model_","",get_class($entity))); ?></button>
				   		<?php if(sizeof($connections) > 0){ ?><a href="<?php echo BASE_URL.$route?>/return_csv/<?php echo $entity->id; ?>"><div class="btn btn-secondary">Download Connection CSV</div></a><?php } ?>
				    <?php else :?>
				        <button type="submit" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add <?php echo Inflector::humanize(str_replace("Model_","",get_class($entity))); ?></button>
				    <?php endif; ?>
			    </div>
		    </div>
		</form>

		<div class="btn-group pull-right">
			<?php if ($entity->id > 0) : ?>
			<form method="post" action="<?php echo BASE_URL.$route; ?>/delete/<?php echo $entity->id; ?>">
			    <button type="submit" class="btn btn-danger"><i class="icon-trash icon-white"></i> Delete</button>
			</form>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($entity->id > 0) :?>
		<?php foreach($connections as $key => $connection) :?>
			<div id="pane<?php echo $key; ?>" class="tab-pane">
				<?php echo $connection['content'] ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	</div>
</div>
