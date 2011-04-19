<?php

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