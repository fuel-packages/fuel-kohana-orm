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
/**
 * Line Change
 * 
 * @original	
 * @modified	namespace Kohana;
 */
namespace Kohana;

/**
 * Model base class. All models should extend this class.
 *
 * @package    Kohana
 * @category   Models
 * @author     Kohana Team
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Model {

	/**
	 * Create a new model instance.
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param   string   model name
	 * @return  Model
	 */
	public static function factory($name)
	{
		// Add the model prefix
		$class = 'Model_'.$name;

		return new $class;
	}

} // End Model
