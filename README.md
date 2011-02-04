##To install this package:

* Through OIL:
		php oil package install kohana-orm
		
* Manually:
		1. Download
		2. Copy to DOCROOT/packages/
		3. Go to DOCROOT/app/config.php and find:
				'packages'	=> array(
					//'activerecord',
				),

		   And add 'orm' to array:
				'packages'	=> array(
					//'activerecord',
					'kohana-orm',
				),
		4. Ensure correct database settings are provided in DOCROOT/app/config/db.php


##Using the ORM:

			// DOCROOT/app/class/controller/welcome.php
			class Controller_Welcome extends Controller {

				public function action_index()
				{
					$clients = Model_Client::init()->where('last_name', '=', 'Corlett')->find_all();
					// Or:
					// $clients = new Model_Client();
					// $clients = $clients->where('last_name', '=', 'Corlett')
					// 					  ->find_all();
					
					foreach ($clients as $client)
					{
						echo nl2br($client->first_name . PHP_EOL);
						
						$client->first_name = 'Another name';
						
						$client->save();
					}
				}
				
				// Additionally, you can use the orm with magic methods as follows
				$clients = Model_Client::find_all_by_last_name('Corlett');
				// Or:
				// $clients = Model_Client::factory()->find_all_by_last_name('Corlett');
				// Or:
				// $clients = new Model_Client();
				// $clients = $clients->find_all_by_last_name('Corlett');
				
				foreach ($clients as $client)
				{
					// Same as $client->first_name except it doesn't throw
					// An error if not existent, just returns false.
					if ($client->get_first_name())
					{
						// As opposed to $client->first_name = 'Another name';
						$client->set_first_name('Another name');
						
						$client->save();
					}
				}
				
				// Due to popular demand, you can use the old factory method,
				// Although this is NOT recommended. For example
				$clients = \ORM::factory('\\clients')->find_all();
				
				// Note, you need to provide the namespace for the model to be
				// loaded using the factory method, replacing single slashes with
				// double slashes (the same as in all your bootstrap.php files).
			}

			// DOCROOT/app/classes/model/client.php
			class Model_Client extends \ORM
			{
				// Relationships are defined as follows:
				
				// Same namespace
				protected $_has_many = array(
					'cars' => array(
						// Yes, I know in Kohana you would
						// have just put 'Client_Car', but
						// the orm now works out what namespace
						// you're in if you don't provide it one,
						// and having to add the 'Model_' prefix
						// is just too many assumptions.
						'model' => 'Model_Client_Car'
				));
				
				// Different namespace
				protected $_has_many = array(
					'cars' => array(
						// Notice the double backslashes!
						'model' => 'Somemodule\\Model_Client_Car'
				));
			}

		See http://kohanaframework.org/guide/orm for full usage instructions (magic methods are not available in original ORM).
		
		
##Note:

	The reason the original method of loading an ORM model ($something = ORM::factory('client'))
	has been removed is because of namespacing. Without making a heap of assumptions, we cannot
	load the correct model.
	
	Say you've got the following app structure:
	
	app/
	---/classes/
	-----------/models/client.php
	
	// And under modules
	-----------/somemodule/
	----------------------/classes/
	------------------------------/models/client.php
	
	Q: If you went ORM::factory('client'), which module would it load?
	A: The one in the same namespace (only if that namespace is the global namespace - no module support).
	
	The way it's structured now, you can load a model from any module, for example (assuming you have the same
	structure as above):
	
	// DOCROOT/app/classes/controller/welcome.php
	class Controller_Welcome extends Controller {

		public function action_index()
		{
			// This will use the model under app/classes/models/client.php
			$clients = Model_Client::factory()->find_by_first_name('ben');
			
			// now let's say you've got a module called `somemodule`, and it's
			// also got a model called `client`. How do we load this? this is
			// how:
			$clients = Somemodule\Model_Client::factory()->find_by_first_name('ben');
			//            ^^^^  notice the namespace
		}
	}
	
	For the namespacing (hmvc) support you need to enable your module in DOCROOT/app/config/config.php (around line 130
	at the time of writing this).
	
	If you have any questions, you can reach me on twitter [@ben_corlett](http://www.twitter.com/ben_corlett) or on [Fuel Forums](http://fuelphp.com/users/profile/view/29).