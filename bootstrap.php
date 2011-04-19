<?php

Autoloader::add_core_namespace('Kohana');

Autoloader::add_classes(array(
	
	// Fuel wrappers
	'Kohana\\Kohana_Exception'					=> __DIR__ . '/classes/exception.php',
	'Kohana\\Model'								=> __DIR__ . '/classes/model.php',
	'Kohana\\Orm'       						=> __DIR__ . '/classes/orm.php',
	'Kohana\\Validation'						=> __DIR__ . '/classes/validation.php',
	
	// Original Kohana classes
	'Kohana\\Kohana_Model'						=> __DIR__ . '/vendor/kohana/model.php',
	'Kohana\\Kohana_ORM'						=> __DIR__ . '/vendor/kohana/orm.php',
	'Kohana\\Kohana_ORM_Validation_Exception'	=> __DIR__ . '/vendor/kohana/orm/validation/exception.php',
));