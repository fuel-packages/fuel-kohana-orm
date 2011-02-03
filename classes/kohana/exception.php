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

class Kohana_Exception extends \Exception {

	/**
	 * Creates a new translated exception.
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
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
	 * @param   object  Exception
	 * @return  string
	 */
	public function exception_text(Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), Kohana::debug_path($e->getFile()), $e->getLine());
	}
} // End Kohana_Exception