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

class Inflector extends \Fuel\Core\Inflector
{
	public static function plural($word)
	{
		return \Fuel\Core\Inflector::pluralize($word);
	}
	
	public static function singular($word)
	{
		return \Fuel\Core\Inflector::singularize($word);
	}
}