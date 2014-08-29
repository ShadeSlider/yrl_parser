<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'iritec');
define('DB_NAME', 'iritec');
define('DB_PASSWORD', '12345');


define('SYSTEM_ROOT_DIR', realpath(__DIR__) . DIRECTORY_SEPARATOR);
define('DATA_DIR', SYSTEM_ROOT_DIR . 'data' . DIRECTORY_SEPARATOR);

registerAutoload();



function registerAutoload()
{
	spl_autoload_register(function ($class) {

		$libFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $class . '.class.php';

		if(file_exists($libFilePath) && is_readable($libFilePath)) {
			include_once($libFilePath);
		}
		else {
			throw new InvalidArgumentException('Class "' . $class . '" does not exist.');
		}
	});
}