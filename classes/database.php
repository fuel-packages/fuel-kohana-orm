<?php
/**
 * Kohana ORM for Fuel.
 *
 * @package		Kohana
 * @category	ORM
 * @author		TJS Technology Pty Ltd
 * @link		http://www.tjstechnology.com.au
 */

namespace Kohana;

abstract class Database extends \Fuel\Core\Database_Connection
{
	// Query types, needed for compatibility with Kohana Database class usage
	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;
}