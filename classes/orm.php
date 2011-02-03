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
		// Set the object name and plural name
		// Namespace Fix
		$this->_object_name   = strtolower(substr(array_pop(explode('\\', get_class($this))), 6));
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
	 * Factory
	 * 
	 * Used to return new instance of 
	 * ORM
	 * 
	 * @access	public
	 * @return	ORM
	 */
	public static function factory()
	{
		return new static;
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
	public function __call($method, $arguments)
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