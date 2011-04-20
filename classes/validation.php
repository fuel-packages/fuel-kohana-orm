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

class Validation extends \Fuel\Core\Validation {
	
	/**
	 * Check
	 * 
	 * Checks the validation object
	 * against the rules provided
	 * 
	 * This is just a wrapper for Kohana
	 * users who are used to calling check()
	 * on a model to validate data
	 * 
	 * @access	public
	 * @return	mixed
	 */
	public function check()
	{
		return parent::run();
	}
}