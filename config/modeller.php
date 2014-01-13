<?php defined('SYSPATH') or die('No direct script access.');

return array(

/* -----------------------------------------------------------------------------
 *  Default Modeller config
 * -----------------------------------------------------------------------------
 */
	'default' => array
	(
		// Route to modeller, will be used for route and directory
        // If set to FALSE, no route will be added
		'route' => 'modeller/<model>(/<action>(/<id>))',
	),

);