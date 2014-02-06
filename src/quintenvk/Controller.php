<?php
namespace quintenvk;
/**
 * a base controller to reroute restful requests to the proper methods & handle default output.
 */
class Controller {

	protected $response = array();
	protected $responseCode = 200;
	protected $contentType = 'json';
	protected $noOutputHandling = false;
	protected static $prefix = 'process';

	public static function dispatchAPICall() {

		//routing to the correct class
		$method = self::$prefix.ucfirst(HTTP::getMethod());
		$arguments = func_get_args();
		$castedArguments = array();

		foreach($arguments as $k => $arg) {
			$castedArguments[$k] = Variable::cast($arg);
		}

		try {
			//create a new instance and call the given method
			$calledClass = get_called_class();
			$instance = new $calledClass;
			call_user_func_array(array($instance,$method),$castedArguments);

			if(!empty($instance)) {
				$instance->doOutput();
				return;
			}
		} catch(Exception $e) {
			$instance = new self();
			$instance->responseCode = 501;
		}
	}

	public function doOutput() {
		if(!$this->noOutputHandling)	{
			$response = $this->response;
			\quintenvk\HTTP::setResponseCode($this->responseCode);
			\quintenvk\HTTP::setContentType($this->contentType);
			if(is_array($response) || is_object($response)) {
				echo json_encode($response);
				return;
			}
			echo $response;
		}
	}

}

