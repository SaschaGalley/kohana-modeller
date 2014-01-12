<h1>
    <?php echo __($model->humanized_plural()); ?>
</h1>

<table class="table table-bordered table-hover table-striped dataTable">
    <thead>
        <tr>
            <th class="hidden"></th>
            <?php foreach($list_headers as $header) :?>
			    <th><?php echo __($header); ?></th>
			<?php endforeach; ?>
        </tr>
    </thead>
    <tbody data-link="row" class="rowlink">
        <?php foreach ($entities as $entity) : ?>
            <tr>
                <td class="hidden">
                    <a href="<?php echo $route; ?>/edit/<?php echo $entity->id; ?>">Edit</a>
                </td>
                <?php foreach ($entity->show_columns() as $column) : ?>
                	<?php if (array_key_exists($column, $entity->belongs_to())) : ?>
                		<td class="nolink">
							<a href="<?php echo $entity->$column->controller_route(); ?>/edit/<?php echo $entity->$column->pk(); ?>">
								<?php echo $entity->$column; ?>
							</a>
						</td>
					<?php elseif (array_key_exists($column, $entity->has_many())) : ?>
						<td class="nolink">
							<a href="<?php echo $entity->controller_route().'/edit/'.$entity->id.'#pane'.Inflector::singular($column); ?>">
								<span class="badge badge-info"><?php echo $entity->$column->count_all()?></span>
							</a>
						</td>
					<?php else: ?>
						<td>
							<?php echo $entity->$column; ?>
						</td>
                	<?php endif; ?>
                <?php endforeach; ?>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="controls">
    <div class="row-fluid">
		<a href="<?php echo $model->controller_route(); ?>/add<?php echo URL::query($filters); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
		<a href="<?php echo $model->controller_route(); ?>/export" class="btn btn-default"><i class="fa fa-download"></i> <?php echo __('Export'); ?></a>
		<a href="javascript: window.print();" class="btn btn-default"><i class="fa fa-print"></i> <?php echo __('Print'); ?></a>
    </div>
</div>