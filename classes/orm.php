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

class Orm extends Kohana_ORM {
	
	/**
	 * Object instance for Singleton access
	 * 
	 * @var	Spark\Object
	 */
	protected static $_instance = null;
	
	/**
	 * Default for_select() property
	 * 
	 * @var	string
	 */
	protected $_for_select = null;
	
	/**
	 * Instance
	 * 
	 * Get the instance of the
	 * class using the Singleton
	 * pattern
	 * 
	 * @access	public
	 * @param	mixed
	 * @return	Kohana\Orm
	 */
	public static function instance()
	{
		// You can't get an instance
		// of this class, but rather
		// of all the child classes
		// that inherit this class
		if (get_called_class() === __CLASS__)
		{
			throw new Exception('static::%s() can only be called on a child class of %s, and not this class itself', __FUNCTION__, __CLASS__);
		}
		
		if (is_null(static::$_instance))
		{
			// Create a reflection class from the called class
			$reflection_class = new \ReflectionClass(get_called_class());

			// Create a new instance of the reflection class and
			// parse the arguments given to this function to the
			// new instance of that class
			static::$_instance = $reflection_class->newInstanceArgs(func_get_args());
		}
		
		return static::$_instance;
	}
	
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
			
			// If the class exists already load it
			// otherwise we'll continue to find
			// the class in namespaces
			if (class_exists($model))
			{
				return new $model($id);
			}
			
			// We need to work out the controller
			// that called the Kohana\Orm::factory()
			$calling_class = debug_backtrace();
			$calling_class = $calling_class[1];
			
			// The class that called this
			$class = $calling_class['class'];
			
			// If this was loaded through a relationship, we can
			// Look at the model to find out what it is
			if (Inflector::get_namespace($class) === 'Kohana\\')
			{
				$class = get_class($calling_class['object']);
			}
			
			// Get the namespace of the calling class
			$namespace = Inflector::get_namespace($class);
			
			// Get the final model to load
			$model = $namespace . $model;
			
			// If the class exists return it
			if (class_exists($model))
			{
				return new $model($id);
			}
			
			// If we haven't found a model
			// to load now, throw an exception
			throw new Kohana_Exception(':method() could not find a class to load - class determined to be loaded is :class', array(
				':method'	=> __METHOD__,
				':class'	=> $model,
			));
		}
		
		// If the person called factory()
		// explicitly through the model
		
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
		
		// Build the validation object with its rules
		$this->_validation = Validation::factory(sprintf('%s%s%s', $this->_object_name, microtime(), rand()));

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
		return null;
	}
	
	/**
	 * Check
	 * 
	 * Validates the current model's data
	 * 
	 * @access	public
	 * @param	Validation	Extra Validation
	 * @return	Kohana\Orm
	 */
	public function check(Validation $extra_validation = null)
	{
		// Run the rules
		$this->rules();
		
		if ($this->_validation->run($this->as_array()))
		{
			return $this;
		}
		
		return false;
	}
	
	/**
	 * Call
	 * 
	 * Magic method used as getters / setters
	 * 
	 * @access	public
	 * @param	string	Method
	 * @param	array	Arguments
	 * @return	mixed
	 */
	public function __call($method, array $arguments)
	{
		// Get the key
		$key = substr($method, 4);
		
		// Check different setters / getters
		// and return results if applicable
		switch (substr($method, 0, 3))
		{
			case 'get':
				
				// There must be a key provided. Not just $model->get();
				if ( ! $key) throw new Kohana_Exception('Invalid method :method called in :class',
					array(':method' => 'get', ':class' => get_class($this)));
				
				return $this->$key;
			case 'set':
				
				// There must be a key provided. Not just $model->set();
				if ( ! $key) throw new Kohana_Exception('Invalid method :method called in :class',
					array(':method' => 'set', ':class' => get_class($this)));
				
				$this->$key = (isset($arguments[0])) ? $arguments[0] : null;
				return $this;
		}
		
		$type	= null;
		$fields	= null;
		$magic	= false;
		
		if (strpos($method, 'count_all_by') === 0)
		{
			$type		= 'count_all';
			$fields		= substr($method, 13);
			$magic		= true;
		}
		
		if (strpos($method, 'find_all_by') === 0)
		{
			$type		= 'find_all';
			$fields		= substr($method, 12);
			$magic		= true;
		}
		
		if (strpos($method, 'find_by') === 0)
		{
			$type		= 'find';
			$fields		= substr($method, 8);
			$magic		= true;
		}
		
		if ($magic === true)
		{
			if ($and_parts = explode('_and_', $fields))
			{
				foreach ($and_parts as $and_part)
				{
					$or_parts = explode('_or_', $and_part);

					if (count($or_parts) == 1)
					{
						$this->_db_pending[] = array('name' => 'where', 'args' => array($or_parts[0], '=', array_shift($arguments)));
					}
					else
					{
						foreach ($or_parts as $or_part)
						{
							$this->_db_pending[] = array('name' => 'or_where', 'args' => array($or_part, '=', array_shift($arguments)));
						}
					}
				}
				
				return $this->$type();
			}
		}
		
		return parent::__call($method, $arguments);
	}
	
	/**
	 * Call Static
	 * 
	 * Static magic method used as getters / setters
	 * 
	 * @access	public
	 * @param	string	Method
	 * @param	array	Arguments
	 * @return	mixed
	 */
	public static function __callStatic($method, array $arguments)
	{
		return static::factory()->__call($method, $arguments);
	}
	
	/**
	 * For Select
	 * 
	 * Prepares the results
	 * for a Form::select();
	 * 
	 * The first parameter is the column
	 * name used as the label for the option
	 * and the second one is used as the value
	 * (defaulted to the primary key)
	 * 
	 * Usage:
	 * 
	 * 		// In Controller
	 * 		$this->response->body = \View::factory('foo/bar')
	 * 									 ->set('countries', Model_Country::factory()->where('code', 'NOT LIKE', 'US')->for_select());
	 * 
	 * 		// In View
	 * 		<?=\Form::select('countries', null, $countries);
	 */
	public function for_select($label = null, $value = null)
	{
		// If a label isn't provided
		// use the for select property
		if ( ! $label)
		{
			// Set the for select property
			if ( ! $this->_for_select) $this->_for_select = $this->pk();
			
			// And the label
			$label = $this->_for_select;
		}
		
		// Select fallback
		$select = array();
		
		// Methods to get data 
		$label_method = sprintf('get_%s', $label);
		
		// Value method
		if ($value === null) $value_method = 'pk';
		elseif ($value === false) $value_method = false;
		else $value_method = $value;
		
		// Loop through and build array
		foreach ($this->find_all() as $result)
		{
			if ($value_method) $select[$result->$value_method()] = $result->$label_method();
			else $select[] = $result->$label_method();
		}
		
		// Return the array
		return $select;
	}
}