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
		
		// Set the object name and plural name
		// Namespace Fix
		$exploded_ns = explode('\\', get_class($this));
		$this->_object_name   = strtolower(substr(array_pop($exploded_ns), 6));
		unset($exploded_ns);
		
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
		return new $model($id);
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
	
	/**
	 * Handles retrieval of all model values, relationships, and metadata.
	 *
	 * @param   string  column name
	 * @return  mixed
	 */
	public function __get($column)
	{
		if (array_key_exists($column, $this->_object))
		{
			$this->_load();

			return $this->_object[$column];
		}
		elseif (isset($this->_related[$column]) AND $this->_related[$column]->_loaded)
		{
			// Return related model that has already been loaded
			return $this->_related[$column];
		}
		elseif (isset($this->_belongs_to[$column]))
		{
			$this->_load();

			$model = $this->_related($column);

			// Use this model's column and foreign model's primary key
			$col = $model->_table_name.'.'.$model->_primary_key;
			$val = $this->_object[$this->_belongs_to[$column]['foreign_key']];

			$model->where($col, '=', $val)->find();

			return $this->_related[$column] = $model;
		}
		elseif (isset($this->_has_one[$column]))
		{
			$model = $this->_related($column);

			// Use this model's primary key value and foreign model's column
			$col = $model->_table_name.'.'.$this->_has_one[$column]['foreign_key'];
			$val = $this->pk();

			$model->where($col, '=', $val)->find();

			return $this->_related[$column] = $model;
		}
		elseif (isset($this->_has_many[$column]))
		{
			$model = $this->_has_many[$column]['model'];
			$model = $this->_get_relationship_class_name($model);
			$model = $model::init();
			
			if (isset($this->_has_many[$column]['through']))
			{
				// Grab has_many "through" relationship table
				$through = $this->_has_many[$column]['through'];

				// Join on through model's target foreign key (far_key) and target model's primary key
				$join_col1 = $through.'.'.$this->_has_many[$column]['far_key'];
				$join_col2 = $model->_table_name.'.'.$model->_primary_key;

				$model->join($through)->on($join_col1, '=', $join_col2);

				// Through table's source foreign key (foreign_key) should be this model's primary key
				$col = $through.'.'.$this->_has_many[$column]['foreign_key'];
				$val = $this->pk();
			}
			else
			{
				// Simple has_many relationship, search where target model's foreign key is this model's primary key
				$col = $model->_table_name.'.'.$this->_has_many[$column]['foreign_key'];
				$val = $this->pk();
			}

			return $model->where($col, '=', $val);
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the :class class',
				array(':property' => $column, ':class' => get_class($this)));
		}
	}
	
	/**
	 * Returns an ORM model for the given one-one related alias
	 *
	 * @param   string  alias name
	 * @return  ORM
	 */
	protected function _related($alias)
	{
		if (isset($this->_related[$alias]))
		{
			return $this->_related[$alias];
		}
		elseif (isset($this->_has_one[$alias]))
		{
			$model = $this->_has_one[$alias]['model'];
			$model = $this->_get_relationship_class_name($model);
			return $model::init();
		}
		elseif (isset($this->_belongs_to[$alias]))
		{
			$model = $this->_belongs_to[$alias]['model'];
			$model = $this->_get_relationship_class_name($model);
			return $model::init();
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Get Relationship Class
	 * 
	 * This method finds the class
	 * that is used for the relationship
	 * of this object. it checks for namespacing
	 * but if no namespace is provided, it uses
	 * the same namespace as the called class.
	 * 
	 * @access	protected
	 * @param	string	relationship class
	 * @return	mixed	intance of class
	 */
	protected function _get_relationship_class_name($relationship_class)
	{
		// if relationship class has namespace
		// in it, use that namespace. otherwise, use
		// the called class' namespace.
		if (strpos($relationship_class, '\\'))
		{
			return $relationship_class;
		}
		else
		{
			// The called class
			$called_class = get_called_class();

			// The called class' namespace
			$called_class_namespace = substr($called_class, 0, -(strlen(strrchr($called_class, '\\'))) + 1);
			
			return $called_class_namespace . $relationship_class;
		}
	}
}