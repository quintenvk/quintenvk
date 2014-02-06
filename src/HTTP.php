<?php
namespace quintenvk;
/**
 * include some convenient & readable methods
 */
class HTTP {
	/**
	 * http response codes
	 */
	protected static $codes = array(
		200 => '200 OK',
		201 => '201 Created',
		202 => '202 Accepted',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		304 => '304 Not Modified',
		307 => '307 Temporary Redirect',
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		406 => '406 Not Acceptable',
		410 => '410 Gone',
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
	);

	/**
	 * http content types
	 */
	protected static $contentTypes = array(
		'json' => 'application/json',
		'html' => 'text/html',
		'text' => 'text/plain',
		'xml'  => 'application/rss+xml'
	);

	public static function getData() {
		switch(self::getMethod()){
			case 'post':
				return $_POST;

			case 'put':
				parse_str(file_get_contents('php://input'), $_PUT);
				return $_PUT;

			case 'delete':
				parse_str(file_get_contents('php://input'), $_DELETE);
				return $_DELETE;

			case 'get':
			default:
				return $_GET;
		}
	}

	public static function getMethod() {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	public static function redirect($URL, $code = 302, $delay = null) {
		self::respond($code);
		if($delay !== null) sleep((int) $delay);
		if(stripos($_SERVER['SERVER_NAME'], $URL) !== false || stripos('http://', $URL) !== false) {
			header('Location: '. $URL);
			exit;
		}
		header(sprintf('Location: http://%s%s', $_SERVER['SERVER_NAME'], $URL));
		exit;
	}

	public static function respond($status, $contentType = null) {
		self::setResponseCode($status);
		if($contentType){
			self::setContentType($contentType);
		}
	}

	public static function setResponseCode($code) {
		header('HTTP/1.1 ' . $code . ' ' . self::$codes[$code]);
	}

	public static function setContentType($contentType) {
		header('Content-Type: ' . self::$contentTypes[$contentType]);
	}
}

class OHTTPException extends \Exception {}
