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

class Kohana_Exception extends \Fuel\Core\Fuel_Exception {
	
	/**
	 * Creates a new translated exception.
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
	 * @access	public
	 * @param   string     error message
	 * @param   array      translation variables
	 * @param   integer    the exception code
	 * @return  void
	 */
	public function __construct($message, array $variables = NULL, $code = 0)
	{
		// Set the message
		$message = empty($variables) ? $message : strtr($message, $variables);

		// Pass the message to the parent
		parent::__construct($message, $code);
	}

	/**
	 * Magic object-to-string method.
	 *
	 *     echo $exception;
	 * 
	 * @access	public
	 * @uses    Kohana::exception_text
	 * @return  string
	 */
	public function __toString()
	{
		return $this->exception_text($this);
	}
	
	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 * 
	 * @access	public
	 * @param   object  Exception
	 * @return  string
	 */
	public function exception_text(Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), Kohana::debug_path($e->getFile()), $e->getLine());
	}
}