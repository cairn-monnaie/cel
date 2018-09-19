<?php namespace Cyclos;

/**
 * Base class for Cyclos service proxies
 */
class Service {
	private $urlSuffix;

	protected function __construct($urlSuffix) {
		$this->urlSuffix = $urlSuffix;
	}
	
	protected function __run($operation, $params) {
		// Setup curl
		$url = Configuration::url($this->urlSuffix);
		$ch = \curl_init($url);
		$options = Configuration::curlOptions($operation, $params);
		\curl_setopt_array ($ch, $options);
		
		// Execute the request
		$json = \curl_exec($ch);
		$code = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code == 0) {
			// The server couldn't be contacted
			throw new ConnectionException();
		}
		$result = \json_decode($json);
		if ($code == 200) {
			return (property_exists($result, "result")) ? $result->result : NULL;
		} else {
			$error = $result;
			if ($error == NULL) {
				$error = new \stdclass();
				$error->errorCode = 'UNKNOWN';
			}
			throw new ServiceException($this->urlSuffix, $operation, $error->errorCode, $error);
		}
	}
}

?>