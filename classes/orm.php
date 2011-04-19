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
	 * the first parameter is the class to
	 * load and the second parameter
	 * is the (optional) record to load
	 * (identified by the primary key)
	 * 
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
			$model = $param_1;
			
			// If the person didn't include 'model'
			// look for a class that includes model
			// in the same namespace as the caller
			if (stripos($param_1, 'model_') === false)
			{
				$model = sprintf('Model_%s', ucfirst($model));
			}
			
			echo '<pre>';
			print_r(debug_backtrace());
			echo '</pre>';
			// return new $model($param_2);
		}
		
		
		// // Set class name
		// $model = 'Model_'.ucfirst($model);
		// 
		// return new $model($id);
	}
}