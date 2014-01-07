<div class="container">
    <div class="row-fluid">
        <table class="table table-bordered table-hover table-striped table-condensed" data-provides="rowlink">
            <thead>
                <tr>
                    <th class="datarow-provider"></th>
                    <?php foreach($list_headers as $header) :?>
					    <th><?php echo $header; ?></th>
					<?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entity_list as $entity) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo BASE_URL.$route; ?>edit/<?php echo $entity->id; ?>">Edit</a>
                        </td>
                        <?php foreach ($entity->show_columns() as $column) : ?>
                        	<?php if (array_key_exists($column, $entity->belongs_to())) : ?>
                        		<td class="nolink">
									<a href="<?php echo BASE_URL.$entity->$column->controller_name(); ?>/edit/<?php echo $entity->$column->pk(); ?>">
										<span class="label label-default"><?php echo $entity->$column; ?></span>
									</a>
								</td>
							<?php elseif (array_key_exists($column, $entity->has_many())) : ?>
								<td class="nolink">
									<a href="<?php echo BASE_URL.$entity->controller_name().'/edit/'.$entity->id.'#pane'.Inflector::singular($column); ?>">
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
    </div>
</div>

<div class="container controls">
    <div class="row-fluid">
		<a href="<?php echo BASE_URL.$route; ?>add<?php echo URL::query($query); ?>" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add</a>
		<a href="<?php echo BASE_URL.$route; ?>export" class="btn"><i class="icon-download-alt"></i> Export</a>
		<a href="javascript: window.print();" class="btn"><i class="icon-print"></i> Print</a>
    </div>
</div>