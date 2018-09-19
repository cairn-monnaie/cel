<?php namespace Cyclos;

/**
 * Exception thrown when the Cyclos server couldn't be contacted (probably offline).
 */
class ConnectionException extends \Exception {
	public function __construct() {
		parent::__construct("Error contacting the Cyclos server");
	}
}

?>