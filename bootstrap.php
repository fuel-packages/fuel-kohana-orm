<?php
/**
 * Kohana orm class.
 *
 * @package		Kohana
 * @category	ORM
 * @author		TJS Technology Pty Ltd
 * @link		http://www.tjstechnology.com.au
 */

Fuel\Core\Autoloader::add_core_namespace('ORM');

Fuel\Core\Autoloader::add_classes(array(
	'ORM\\ORM'					=> __DIR__.'/classes/orm.php',
));

/* End of file bootstrap.php */