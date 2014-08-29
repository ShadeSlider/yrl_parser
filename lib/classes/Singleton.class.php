<?php
/**
  * User: Eric Gorbikov
 * Date: 8/26/13
 * Time: 6:48 AM
  */

abstract class Singleton {

	protected static $instances;

	private function __construct() {/* No direct instantiation */}

	public static function getInstance($class = null)
	{
		if(!$class)
			$class = __CLASS__;

		if(!isset(self::$instances[$class])) {
			return $object = new $class;
		}
		else {
			return self::$instances[$class];
		}
	}
}