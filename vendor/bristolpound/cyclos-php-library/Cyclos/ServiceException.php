<?php namespace Cyclos;

/**
 * Exception thrown when there is some error in a cyclos service call.
 * Normally, clients should handle the $errorCode property, for strings like PERMISSION_DENIED or INSUFFICIENT_BALANCE.
 * The $error property contains additional error details.
 */
class ServiceException extends \Exception {
	public $service;
	public $operation;
	public $errorCode;
	public $error;

	public function __construct($service, $operation, $errorCode, $error) {
		parent::__construct("Error calling Cyclos service: ${service}.${operation}: $errorCode");
		$this->service = $service;
		$this->operation = $operation;
		$this->errorCode = $errorCode;
		$this->error = $error;
	}
}

?>