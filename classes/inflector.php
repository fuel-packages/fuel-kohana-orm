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

class Inflector extends \Fuel\Core\Inflector {
	
	/**
	 * Plural
	 * 
	 * Gets the plural value
	 * for a given word
	 * 
	 * @access	public
	 * @param	string	Word
	 * @return	string	Word
	 */
	public static function plural($word)
	{
		return parent::pluralize($word);
	}
	
	/**
	 * Singular
	 * 
	 * Gets the singular value
	 * for a given word
	 * 
	 * @access	public
	 * @param	string	Word
	 * @return	string	Word
	 */
	public static function singular($word)
	{
		return parent::singularize($word);
	}
}