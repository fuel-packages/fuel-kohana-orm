<?php
/**
 * Kohana Orm
 * 
 * The Kohana Orm Fuel package is
 * a port of the Kohana Orm
 * 
 * @package		Fuel
 * @subpackage	Kohana Orm
 * @version		3.1.2
 * @author		Ben Corlett
 * @company		TJS Technology
 * @website		http://www.tjstechnology.com.au
 * @license		See LICENSE.md
 */
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