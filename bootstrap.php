<?php
/**
 * Kohana ORM for Fuel.
 *
 * @package		Kohana
 * @category	ORM
 * @author		TJS Technology Pty Ltd
 * @link		http://www.tjstechnology.com.au
 */

Autoloader::add_core_namespace('Kohana');

Autoloader::add_classes(array(
	'Kohana\\Database'				=> __DIR__ . '/classes/database.php',
	'Kohana\\DB'					=> __DIR__ . '/classes/db.php',
	'Kohana\\Inflector'				=> __DIR__ . '/classes/inflector.php',
	'Kohana\\Kohana_ORM'			=> __DIR__ . '/classes/kohana/orm.php',
	'Kohana\\Kohana_Exception'		=> __DIR__ . '/classes/kohana/exception.php',
	'Kohana\\Orm'					=> __DIR__ . '/classes/orm.php',
));

/* End of file bootstrap.php */