<?php

namespace Kohana;

class Orm extends Kohana_ORM {
	
	/**
	 * Factory
	 * 
	 * Creates a new instance of the class
	 * 
	 * If factory() is called on a child
	 * of Kohana\Orm, the first parameter
	 * is the (optional) record to load
	 * (identified by the primary key)
	 * 
	 * If factory() is falled on Kohana\Orm
	 * the first parameter is the model to
	 * load and the second parameter
	 * is the (optional) record to load
	 * (identified by the primary key).
	 * 
	 * You don't have to include the 'Model_'
	 * part of the model, so providing ('user')
	 * would look for 'Model_User' in the same
	 * namespace as the calling class
	 * 
	 * @access	public
	 * @param	mixed
	 * @param	mixed
	 * @return	Kohana\Orm
	 */
	public static function factory($param_1 = null, $param_2 = null)
	{
		// If the person called Kohana\Orm::factory()
		if (get_called_class() === __CLASS__)
		{
			// The model to load
			$model	= $param_1;
			
			// The Id to load
			$id		= $param_2;
			
			// If the person didn't include 'model'
			// look for a class that includes model
			// in the same namespace as the caller
			if (stripos($param_1, 'model_') === false)
			{
				$model = sprintf('Model_%s', ucfirst($model));
			}
			
			if (class_exists($model))
			{
				return new $model($id);
			}
			
			
			// We need to work out the controller
			// that called the Kohana\Orm::factory()
			$calling_class = debug_backtrace();
			$calling_class = $calling_class[1]['class'];
			
			// Get the namespace of the calling class
			$calling_namespace = Inflector::get_namespace($calling_class);
			
			// If the class exists return it
			if (class_exists($calling_namespace . $model))
			{
				$model = $calling_namespace . $model;
				
				return new $model($id);
			}
			
			return;
		}
		
		// The Id to load
		$id = $param_1;
		
		// Return the called class
		return new static($id);
	}
	
	/**
	 * Initialize
	 * 
	 * Prepares the model database connection,determines the table name,
	 * and loads column information.
	 *
	 * @access	protected
	 */
	protected function _initialize()
	{
		// Set the object name and plural name
		$this->_object_name		= strtolower(substr(\Inflector::denamespace(get_class($this)), 6));
		$this->_object_plural	= Inflector::plural($this->_object_name);
		
		if ( ! is_object($this->_db))
		{
			// Get database instance
			$this->_db = Database::instance($this->_db_group);
		}

		if (empty($this->_table_name))
		{
			// Table name is the same as the namespace (module) plus the object name
			$this->_table_name = strtolower(str_replace('\\', '_', \Inflector::get_namespace(get_class($this)))) . $this->_object_name;

			if ($this->_table_names_plural === TRUE)
			{
				// Make the table name plural
				$this->_table_name = Inflector::plural($this->_table_name);
			}
		}

		foreach ($this->_belongs_to as $alias => $details)
		{
			$defaults['model'] = $alias;
			$defaults['foreign_key'] = $alias.$this->_foreign_key_suffix;

			$this->_belongs_to[$alias] = array_merge($defaults, $details);
		}

		foreach ($this->_has_one as $alias => $details)
		{
			$defaults['model'] = $alias;
			$defaults['foreign_key'] = $this->_object_name.$this->_foreign_key_suffix;

			$this->_has_one[$alias] = array_merge($defaults, $details);
		}

		foreach ($this->_has_many as $alias => $details)
		{
			$defaults['model'] = Inflector::singular($alias);
			$defaults['foreign_key'] = $this->_object_name.$this->_foreign_key_suffix;
			$defaults['through'] = NULL;
			$defaults['far_key'] = Inflector::singular($alias).$this->_foreign_key_suffix;

			$this->_has_many[$alias] = array_merge($defaults, $details);
		}

		// Load column information
		$this->reload_columns();

		// Clear initial model state
		$this->clear();
	}
	
	/**
	 * Validation
	 * 
	 * Initializes validation rules, and labels
	 *
	 * @access	protected
	 */
	protected function _validation()
	{
		// Still need to add
		// validation or use Fuel's Validation
	}
	
	/**
	 * Check
	 * 
	 * Validates the current model's data
	 * 
	 * @access	protected
	 * @param	Validation	Extra Validation
	 * @return	Kohana\Orm
	 */
	public function check(Validation $extra_validation = NULL)
	{
		// Still need to add
		// validation or use Fuel's Validation
		return $this;
	}
	
	/**
	 * Call
	 * 
	 * Magic method used as getters / setters
	 * 
	 * @access	public
	 * @param   string  $method Method name
	 * @param   array   $args   Method arguments
	 * @return  mixed
	 */
	public function __call($method, $arguments)
	{
		// Get the key
		$key = substr($method, 4);
		
		// Check different setters / getters
		// and return results if applicable
		switch (substr($method, 0, 3))
		{
			case 'get':
				return $this->$key;
			case 'set':
				$this->$key = (isset($arguments[0])) ? $arguments[0] : null;
				return $this;
				// return $this->set_data($key, isset($arguments[0]) ? $arguments[0] : null);
		}
		
		// if ($method == '_init')
		// {
		// 	return;
		// }
		// 
		// // Start with count_by? Get counting!
		// if (strpos($method, 'count_by') === 0)
		// {
		// 	$find_type = 'count';
		// 	$fields = substr($method, 9);
		// }
		// 
		// // Otherwise, lets find stuff
		// elseif (strpos($method, 'find_') === 0)
		// {
		// 	$find_type = strncmp($method, 'find_all_by_', 12) === 0 ? 'all' : (strncmp($method, 'find_by_', 8) === 0 ? 'first' : false);
		// 	$fields = $find_type === 'first' ? substr($method, 8) : substr($method, 12);
		// }
		// 
		// // God knows, complain
		// else
		// {
		// 	throw new \Fuel_Exception('Invalid method call.  Method '.$method.' does not exist.', 0);
		// }
		// 
		// $where = $or_where = array();
		// 
		// if (($and_parts = explode('_and_', $fields)))
		// {
		// 	foreach ($and_parts as $and_part)
		// 	{
		// 		$or_parts = explode('_or_', $and_part);
		// 
		// 		if (count($or_parts) == 1)
		// 		{
		// 			$where[] = array($or_parts[0] => array_shift($args));
		// 		}
		// 		else
		// 		{
		// 			foreach($or_parts as $or_part)
		// 			{
		// 				$or_where[] = array($or_part => array_shift($args));
		// 			}
		// 		}
		// 	}
		// }
		// 
		// $options = count($args) > 0 ? array_pop($args) : array();
		// 
		// if ( ! array_key_exists('where', $options))
		// {
		// 	$options['where'] = $where;
		// }
		// else
		// {
		// 	$options['where'] = array_merge($where, $options['where']);
		// }
		// 
		// if ( ! array_key_exists('or_where', $options))
		// {
		// 	$options['or_where'] = $or_where;
		// }
		// else
		// {
		// 	$options['or_where'] = array_merge($or_where, $options['or_where']);
		// }
		// 
		// if ($find_type == 'count')
		// {
		// 	return static::count($options);
		// }
		// 
		// else
		// {
		// 	return static::find($find_type, $options);
		// }
		
		return parent::__call($method, $arguments);
	}
}