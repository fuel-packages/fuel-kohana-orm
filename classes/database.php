<?php

namespace Kohana;

abstract class Database extends \Fuel\Core\Database_Connection {
	
	/**
	 * Database constants
	 * 
	 * @var	int
	 */
	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;
}