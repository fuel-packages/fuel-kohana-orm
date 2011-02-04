<?php
/**
 * Kohana ORM for Fuel.
 *
 * @package		Kohana
 * @category	ORM
 * @author		TJS Technology Pty Ltd
 * @link		http://www.tjstechnology.com.au
 */

namespace ORM;

class ORM extends Kohana_ORM
{
	/**
	 * Construct
	 * 
	 * Called when the class instance
	 * is created
	 * 
	 * @access	public
	 * @return	ORM
	 */
	public function __construct($id = NULL)
	{
		/*
		 This first bit of code ensures that the user doesn't
		 try to load the orm by going ORM::factory(), but rather
		 loads the child model (which extends this class).
		 */
		
		// The class (this class, without namespace)
		$exploded_ns = explode('\\', __CLASS__);
		$actual_class = array_pop($exploded_ns);
		unset($exploded_ns);
		
		// The class called by the user
		$exploded_ns = explode('\\', get_called_class());
		$called_class = array_pop($exploded_ns);
		unset($exploded_ns);
		
		
		
		// If they're the same (user called ORM::factory() or new ORM())
		if ($actual_class == $called_class)
		{
			throw new Kohana_Exception('You must call the child class, not the ORM class.');
		}
		
		// Set the object name and plural name
		// Namespace Fix
		$exploded_ns = explode('\\', get_class($this));
		$this->_object_name   = strtolower(substr(array_pop($exploded_ns), 6));
		unset($exploded_ns);
		// $this->_object_name   = strtolower(substr(array_pop(explode('\\', get_class($this))), 6));
		$this->_object_plural = Inflector::plural($this->_object_name);

		if ( ! isset($this->_sorting))
		{
			// Default sorting
			$this->_sorting = array($this->_primary_key => 'ASC');
		}

		if ( ! empty($this->_ignored_columns))
		{
			// Optimize for performance
			$this->_ignored_columns = array_combine($this->_ignored_columns, $this->_ignored_columns);
		}

		// Initialize database
		$this->_initialize();

		// Clear the object
		$this->clear();

		if ($id !== NULL)
		{
			if (is_array($id))
			{
				foreach ($id as $column => $value)
				{
					// Passing an array of column => values
					$this->where($column, '=', $value);
				}

				$this->find();
			}
			else
			{
				// Passing the primary key

				// Set the object's primary key, but don't load it until needed
				$this->_object[$this->_primary_key] = $id;

				// Object is considered saved until something is set
				$this->_saved = TRUE;
			}
		}
		elseif ( ! empty($this->_preload_data))
		{
			// Load preloaded data from a database call cast
			$this->_load_values($this->_preload_data);

			$this->_preload_data = array();
		}
	}
	
	/**
	 * Factory (Depreciated)
	 * 
	 * Used to return new instance of 
	 * ORM. Note this function is only for backwards
	 * compatibility (sort of), and you should use
	 * static::init() instead (or magic methods).
	 * 
	 * @access	public
	 * @param	string	model name (not used).
	 * @param	int		id of record to load
	 * @return	ORM
	 */
	public static function factory($model = NULL, $id = NULL)
	{
		// Lets log that it's depreciated
		\Log::info(sprintf('Method %s() is depreciated. You should start using ORM\ORM::init();', __METHOD__));
		
		return new static($id);
	}
	
	/**
	 * Init
	 * 
	 * Used to return new instance of 
	 * ORM.
	 * 
	 * The reason for this method
	 * ontop of static::factory() is that
	 * this method allows you to put an id
	 * in as a parameter, without having to
	 * put NULL as the model parameter (as it
	 * isn't used). Unfortunately, being a static
	 * method, static::factory() must support
	 * the same parameters as the static method in
	 * the parent class, Kohana_ORM. Supid eh? If
	 * you don't, you get the following error:
	 * 
	 * 		Declaration of ORM\ORM::factory() should be compatible with that of ORM\Kohana_ORM::factory()
	 * 
	 * Because I don't want to modify the original
	 * class, we'll just use init to allow chaining
	 * of methods in an ORM descendent class.
	 * 
	 * This method supports a single parameter, namely
	 * id. This allows you do init the class with the record of
	 * id $id to be loaded. For example:
	 * 
	 * 		// This will return record where primary 
	 * 		// Key is 3.
	 * 		$client = Model_Clients::init(3);
	 * 
	 * @access	public
	 * @param	int		id of record to laod
	 * @return	ORM
	 */
	public static function init($id = NULL)
	{
		return new static($id);
	}
	
	/**
	 * Call Static
	 * 
	 * Static Magic Methods
	 * 
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	public static function __callStatic($method, $arguments)
	{
		if (strpos($method, 'find_') === 0)
		{
			// Create new class
			$orm = new static;
			
			// Determine find type
			$find_type = strncmp($method, 'find_all_by_', 12) === 0 ? 'find_all' : (strncmp($method, 'find_by_', 8) === 0 ? 'find' : false);
			
			// What we're finding
			$method = $find_type === 'find' ? substr($method, 8) : substr($method, 12);
			
			// Get the and parts
			$and_parts = explode('_and_', $method);
			
			// Load another instance of this model to find out what the table name is
			$table_name = $orm->_table_name;
			
			foreach ($and_parts as $and_part)
			{
				$or_parts = explode('_or_', $and_part);
				
				if (count($or_parts) == 1)
				{
					$orm->where($or_parts[0], '=', array_shift($arguments));
				}
				else
				{
					foreach($or_parts as $or_part)
					{
						$orm->or_where($or_parts, '=', array_shift($arguments));
					}
				}
			}
			
			return $orm->{$find_type}();
		}
	}
	
	/**
	 * Call 
	 * 
	 * Magic Methods
	 * 
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	public function __call($method, array $arguments)
	{
		if (strpos($method, 'find_') === 0)
		{
			// Determine find type
			$find_type = strncmp($method, 'find_all_by_', 12) === 0 ? 'find_all' : (strncmp($method, 'find_by_', 8) === 0 ? 'find' : false);
			
			// What we're finding
			$method = $find_type === 'find' ? substr($method, 8) : substr($method, 12);
			
			// Get the and parts
			$and_parts = explode('_and_', $method);
			
			// Load another instance of this model to find out what the table name is
			$table_name = $this->_table_name;
			
			foreach ($and_parts as $and_part)
			{
				$or_parts = explode('_or_', $and_part);
				
				if (count($or_parts) == 1)
				{
					$this->where($or_parts[0], '=', array_shift($arguments));
				}
				else
				{
					foreach($or_parts as $or_part)
					{
						$this->or_where($or_parts, '=', array_shift($arguments));
					}
				}
			}
			
			return $this->{$find_type}();
		}
		else if (strpos($method, 'get_') === 0)
		{
			// Work out what we want to set
			$to_get = substr($method, 4);
			
			// check to see if it exists
			if (array_key_exists($to_get, $this->_object))
			{
				return $this->{$to_get};
			}
			
			return false;
		}
		else if (strpos($method, 'set_') === 0)
		{
			// Work out what we want to set
			$to_set = substr($method, 4);
			
			if (array_key_exists($to_set, $this->_object))
			{
				return $this->_object[$to_set] = array_shift($arguments);
			}
		}
		
		return parent::__call($method, $arguments);
	}
}