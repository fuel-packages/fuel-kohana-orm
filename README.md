#Kohana Orm for Fuel
The Kohana Orm Fuel package is a port of the [Kohana Orm](http://kohanaframework.org/3.0/guide/orm) (from version 3.1.2) to [Fuel PHP](http://www.fuelphp.com).

---
###Developers
* Ben Corlett ([TJS Technology](http://www.tjstechnology.com.au)) - [@ben_corlett](http://twitter.com/ben_corlett)
* Thomas Stevens ([TJS Technology](http://www.tjstechnology.com.au)) - [@tomo89aus](http://twitter.com/tomo89aus)

---
##Installing Kohana Orm For Fuel
* Firstly, you need to download the package. This can be done by using Fuel's [Oil](http://fuelphp.com/docs/packages/oil/package.html) utility, run the following command:

		php oil package install kohana-orm
alternatively, you can clone the package to the packages path

		cd APPPATH/fuel/packages/
		git clone git://github.com/TJS-Technology/fuel-kohana-orm.git kohana-orm
or manually downloading the package by selecting **Downloads** up the top right hand corner and renaming the download to kohana-orm and moving the folder to <code>APPPATH/fuel/packages/</code>

* Secondly, you need to enable the package. Go to the following file <code>APPPATH/config/config.php</code> and find the following line

		// On a standard install
		// this is around line 134
		'packages'	=> array(
			//'orm',
		),
and make it look like this

		'packages => array(
			//'orm',
			'kohana-orm',
		),

* Next, there is no next. That's it!

---
##Using the Kohana Orm:
The following examples demonstrate the usage of the Kohana Orm by creating a simple citizen model with a relationship to a country model. Please runt the following SQL on your database

		# Dump of table citizens
		# ------------------------------------------------------------

		DROP TABLE IF EXISTS `citizens`;

		CREATE TABLE `citizens` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `first_name` varchar(255) DEFAULT NULL,
		  `last_name` varchar(255) DEFAULT NULL,
		  `country_id` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

		LOCK TABLES `citizens` WRITE;
		/*!40000 ALTER TABLE `citizens` DISABLE KEYS */;
		INSERT INTO `citizens` (`id`,`first_name`,`last_name`,`country_id`)
		VALUES
			(1,'Ben','Corlett',1),
			(2,'Thomas','Stevens',2),
			(3,'Laura','Schreiber',2),
			(4,'John','Smith',3),
			(5,'Ben','Wise',3),
			(6,'William','Smith',1);

		/*!40000 ALTER TABLE `citizens` ENABLE KEYS */;
		UNLOCK TABLES;


		# Dump of table countries
		# ------------------------------------------------------------

		DROP TABLE IF EXISTS `countries`;

		CREATE TABLE `countries` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

		LOCK TABLES `countries` WRITE;
		/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
		INSERT INTO `countries` (`id`,`name`)
		VALUES
			(1,'Australia'),
			(2,'South Africa'),
			(3,'New Zealand');

		/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
		UNLOCK TABLES;

###Creating your models
* For more see [here](http://kohanaframework.org/3.0/guide/orm/models)
* Navigate to <code>APPPATH/classes/model/</code> and create a file called <code>citizen.php</code>. In it place the following

		<?php

		class Model_Citizen extends Kohana\Orm {
			
		}
* Navigate to <code>APPPATH/classes/model/</code> and create a file called <code>country.php</code>. In it place the following

		<?php

		class Model_Country extends Kohana\Orm {
			
		}
###Basic Usage
####Loading a model
* Navigate to <code>APPPATH/classes/controller/</code> and create a file called <code>citizens.php</code>. In it place the following

		<?php

		class Controller_Citizens extends Controller {

			public function action_index()
			{
				$citizens = Model_Citizens::factory()->find_all();
				
				// Models can be loaded like in Kohana by providing the
				// model name
				// $citizens = Kohana\Orm::factory('citizen')->find_all();
			}
		}
* There is more than one way to load a model
	* You can call factory explicitly on a class that extends <code>Kohana\Orm</code>
	* You can provide the model name to the factory method on <code>Kohana\Orm</code>. **Note:** if you are using modules and are in a namespace, when using the Kohana Orm this way, the model that the Orm looked for will be the in the same namespace as the class that calls this function. If you want to jump between namespaces, you must provide the full name of the model and the namespace as the parameter
	
			/**
			 * This Demonstration isn't using the Citizen / Country example
			 */
			
		 	/**
		 	 * In APPPATH/modules/myfirstmodule/classes/controller/index.php
			 */
			
			// This will look for APPPATH/modules/myfirstmodule/classes/model/user.php
			$users = \Kohana\Orm::factory('users')->find_all();
			
			/**
			 * In APPPATH/modules/mysecondmodule/classes/controller/index.php
			 */
			
			// This will look for APPPATH/modules/mysecondmodule/classes/model/user.php
			$users = \Kohana\Orm::factory('users')->find_all();
			
			// If you want the <code>User</code> model
			// from the <code>Myfirstmodule</code> namespace
			$users = \Kohana\Orm::factory('\\Myfirstmodule\\Model_Users')->find_all();
			
			// But this could be done in a more elegent way
			$users = \Myfirstmodule\Model_Users::factory()->find_all();
			
			/**
			 * As you can see, the Kohana\Orm::factory() method can be
			 * convenient sometimes, however when you are jumping
			 * between namespaces it might be advantageous to call the
			 * model explicitly
			 */
####Inserting Data
* There are two ways to insert data into a model
	* You can use traditional property setting to set values of a model. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following
	
			$citizen = Model_Citizen::factory()
			
			$citizen->first_name = 'Ben';
			$citizen->last_name = 'Corlett';
			$citizen->save();
	* You can use setters / getters to set data. These are good because they're chainable. In the same function as the above example, replace it's contents with
	
			// Create a new citizen
			$citizen = Model_Citzen::factory();
			
			// This could also be
			$citizen = new Model_Citizen();
			
			$citizen->set_first_name('Ben')
					->set_last_name('Corlett')
					->save();
			
			// This can be shortened even further
			
			Model_Citizen::factory()
						 ->set_first_name('Ben')
						 ->set_last_name('Corlett')
						 ->save();



####Finding an Object
* To find an object you can use several methods
	* You can find a object by it's primary key. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following
	
			// This will load a citizen from the database
			// where the primary key (usually id) is 3
			$citizen = Model_Citizen::factory()->find(3);
			
			// This can be shortened to
			$citizen = Model_Citizen::factory(3);
			
			// Or of course using factory on the Kohana\Orm class directly
			$citizen = Kohana\Orm::factory('citizen', 3);
	* You can use any of the following database methods on your object to filter results
		* <code>'where'</code> <code>'and_where'</code> <code>'or_where'</code> <code>'where_open'</code> <code>'and_where_open'</code> <code>'or_where_open'</code> <code>'where_close'</code>
		* <code>'and_where_close'</code> <code>'or_where_close'</code> <code>'distinct'</code> <code>'select'</code> <code>'from'</code> <code>'join'</code> <code>'on'</code> <code>'group_by'</code>
		* <code>'having'</code> <code>'and_having'</code> <code>'or_having'</code> <code>'having_open'</code> <code>'and_having_open'</code> <code>'or_having_open'</code>
		* <code>'having_close'</code> <code>'and_having_close'</code> <code>'or_having_close'</code> <code>'order_by'</code> <code>'limit'</code> <code>'offset'</code> <code>'cached'</code>
	
	* Instructions on using these can be found [here](http://kohanaframework.org/3.0/guide/database/query/builder). Once you have applied all filters you can either call <code>find()</code> to find the first result or <code>find_all()</code> to find all results that match these filters. Ready for a surprise, if you call <code>find_all()</code> on the object without any filters you will get all results.
	
			$citizen = Model_Citizen::factory(/* Notice no primary key here */)->where('first_name', '=', 'Ben')->find();
			
	* You can find a object you can use magic methods. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following
	
			// Find a citizen
			$citizen = Model_Citizen::factory()->find_by_first_name_and_last_name('Ben', 'Corlett');
			
			// This is the same as
			$citizen = Model_Citizen::factory()->where('first_name', '=', 'Ben')->where('last_name', '=', 'Corlett')->find();
			
			// Find all citizens where the first name is Ben or the last name is Smith
			$citizens = Model_Citizen::factory()->find_all_by_first_name_or_last_name('Ben, 'Smith');
			
			// This is the same as
			$citizens = Model_Citizen::factory()->where('first_name', '=', 'Ben')->or_where('last_name', '=', 'Smith')->find_all();
			
			// Count all citizens where the last name is Smith
			$count = Model_Citizen::factory()->count_all_by_last_name('Smith');
			
			// This is the same as
			$count = Model_Citizen::factory()->where('last_name', '=' 'Smith')->count_all();
			
			/**
			 * These magic methods can be called statically, please note
			 * that find(), find_all() and count() on their own cannot be called statically
			 */
			$citizen	= Model_Citizen::find_by_first_name_and_last_name('Ben', 'Corlett');
			$citizens	= Model_Citizen::find_all_by_first_name_or_last_name('Ben, 'Smith');
			$count		= Model_Citizen::count_all_by_last_name('Smith');

####Getting Values
* To get values of an object there are two main ways
	* You can use traditional property getting. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following
	
			// What's Laura's last name?
			$citizen = Model_Citizen::find_by_first_name('Laura');
			
			// Ahh, that's her last name
			echo $citizen->last_name;
			
			// Say someone doesn't have a 'hair_colour' in our database. We'll default them to 'blonde'
			if ( ! $citizen->hair_colour)
			{
				$citizen->hair_colour = 'blonde';
				$citizen->save();
			}
	* You can use setters / getters to get data. In the same function as the above example, replace it's contents with
	
			// Laura has got married
			$citizen = Model_Citizen::find_by_first_name_and_last_name('Laura', 'Smith');
			
			// Ahh, that's her last name
			echo $citizen->get_last_name();
			
			// Say someone doesn't have a 'hair_colour' in our database. We'll default them to 'blonde'
			if ( ! $citizen->get_hair_colour())
			{
				$citizen->set_hair_colour('blonde')
						->save();
			}

####Relationships
* For more see [here](http://kohanaframework.org/3.0/guide/orm/relationships)
* In our example we've only really dealt with citizens and not countries. Navigate to <code>APPPATH/classes/model/citizen.php</code> and add the following

		<?php

		class Model_Citizen extends Kohana\Orm {

			protected $_belongs_to = array(
				'country'	=> array(

					/**
					 * None of this data is needed but you can overwrite it
					 */

					// The model to look for, Model_Country in this case
					// If this were in a namespace you could include the full class name
					// e.g. 'model' => '\\Mysecondmodule\\Model_Country',
					// 'model'		=> 'country',

					// The key in THIS table that makes the link between this
					// object and the primary key of the related model
					// 'foreign_key'	=> 'country_id',
				),
			);
		}

* Navigate to Navigate to <code>APPPATH/classes/model/country.php</code> and add the following and add the follwing code

		<?php

		class Model_Citizen extends Kohana\Orm {

			protected $_has_many = array(
				'citizens'	=> array(

					/**
					 * None of this data is needed but you can overwrite it
					 */

					// The model to look for, Model_Country in this case
					// If this were in a namespace you could include the full class name
					// e.g. 'model' => '\\Mysecondmodule\\Model_Country',
					// 'model'		=> 'citizen',
				),
			);
		}

* You have now setup your relationship. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following

		// Get a citizen
		$citizen = Model_Citizen::factory()->find_by_first_and_last_name('Ben', 'Corlett');
		
		// To get the country you can use traditional properties
		echo $citizen->country->name;
		
		// Or, like before, you can use setters / getters
		echo $citizen->get_country()->get_name();
		
		// Give us some feedback
		echo sprintf('%s %s is a citizen of %s', $citizen->get_first_name(), $citizen->get_last_name(), $citizen->get_country()->get_name());

####Validation
* Before using validation you need to learn Fuel's [Validation](http://fuelphp.com/docs/classes/validation.html) class.
* Let's add validation to our model. Navigate to <code>APPPATH/classes/model/citizen.php</code> and add the following under the <code>protected $_belongs_to</code> property

		public function rules()
		{
			// $this->validation() gets the Fuel Validation class object
			// for this object
			
			// Create rules (this is exactly the same as in the Validation documentation)
			$this->validation()->add('first_name', 'First name')
							   ->add_rule('required');

			$this->validation()->add('last_name', 'Last name')
							   ->add_rule('required');
		}
* Let's check our validation. Navigate to the <code>action_index()</code> function of <code>APPPATH/classes/controller/citizens.php</code>. In it, remove the current contents and place the following

		// Create a new citizen
		$citizen = Model_Citizen::factory();
		
		// Set values. Usually this function would occure on
		// a POST request - $citizen->values($_POST);
		$citizen->values(array(
			'first_name'	=> 'Ben',
		));
		
		// Check our model
		if ($citizen->check())
		{
			// Save our model - it's passed validation
			$citizen->save();
		}
		else
		{
			foreach ($citizen->validation()->errors() as $error)
			{
				echo $error;
			}
		}

---
##Note
* If you find a bug or would like an improvement, please do [fork the code](https://github.com/tjs-technology/fuel-kohana-orm/fork) and fix it or just let me know by [creating a new issue](https://github.com/tjs-technology/fuel-kohana-orm/issues).