<?php
namespace quintenvk;
/**
 * just another class to do common operations on various types of variables.
 */
class Variable {
	public static function toNumber($string_number) {
		$number = (double)str_replace(',', '.', $string_number);
		return $number;
	}

	//redundant? nopes, we skip private vars with this beauty
	public function get_object_vars($obj) {
		return get_object_vars($obj);
	}

	public static function array_get_object($from, $className = stdClass) {
		$obj = new $className();
		foreach($from as $k => $v){
			$obj->$k = $v;
		}
		return $obj;
	}

	public static function mergeObjects(){
		$arguments = func_get_args();
		$object = array_shift($arguments);
		foreach($arguments as $argument){
			$vars = get_object_vars($argument);
			foreach($vars as $k => $v) {
				$object->$k = $v;
			}
		}
		return $object;
	}

    public static function createNesting(array $source, $parent = 0, $keyChildren = 'children', $keyId = 'id', $keyparent = 'parent', array &$dest = array()){
		foreach($source as &$element){
			if(is_object($element)){
				$element = get_object_vars($element);
			}

			if($element[$keyparent] == $parent){
				$element[$keyChildren] = array();
				self::createNesting($source, $element[$keyId], $keyChildren, $keyId, $keyparent, $element[$keyChildren]);
				if(!$element[$keyChildren]){
					unset($element[$keyChildren]);
				}
				$dest[] = $element;
			}
		}

		return $dest;
	}

	public static function setFlags($var, $flags) {
		return $var | $flags;
	}

	public static function unsetFlags($var, $flags) {
		return $var & ~$flags;
	}

	public static function hasFlag($var, $flag) {
		return $var & $flag;
	}

	public static function updatePropertiesForObject(&$object, $properties) {
		if(!is_array($properties)) {
			$properties = get_object_vars($properties);
		}
		foreach($properties as $prop => $value) {
			$object->$prop = $value;
		}
	}

	public static function cast($arg) {
		if(in_array(strtolower($arg), array('true', 'false'))) {
			return (bool) $arg;
		}
		switch(true) {
			case is_float($arg): return (float)$arg;
			case is_int($arg): return (int)$arg;
		}
		return $arg;
	}

	public static function indexBy($array, $key) {
		$indexedArray = array();
		foreach($array as $value) {
			$indexKey = is_object($value) ? $value->$key : $value[$key];
			$indexedArray[$indexKey] = $value;
		}
		return $indexedArray;
	}
}