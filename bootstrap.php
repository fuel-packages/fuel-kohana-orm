<?php
/**
 * Kohana ORM for Fuel.
 *
 * @package		Kohana
 * @category	ORM
 * @author		TJS Technology Pty Ltd
 * @link		http://www.tjstechnology.com.au
 */

Fuel\Core\Autoloader::add_core_namespace('ORM');

Fuel\Core\Autoloader::add_classes(array(
	'ORM\\Database'				=> __DIR__ . '/classes/database.php',
	'ORM\\DB'					=> __DIR__ . '/classes/db.php',
	'ORM\\Inflector'			=> __DIR__ . '/classes/inflector.php',
	
	'ORM\\Kohana_Exception'		=> __DIR__ . '/classes/kohana/exception.php',
	'ORM\\Kohana_Model'			=> __DIR__ . '/classes/kohana/model.php',
	'ORM\\Kohana_ORM'			=> __DIR__ . '/classes/kohana/orm.php',
	
	'ORM\\Model'				=> __DIR__ . '/classes/model.php',
	
	'ORM\\ORM'					=> __DIR__ . '/classes/orm.php',
));

/* End of file bootstrap.php */