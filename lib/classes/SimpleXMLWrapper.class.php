<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

/**
 * @method void addChild($name, $value = null, $namespace = null)
 * @method SimpleXMLElementExtended addChildWithCDATA($name, $text)
 * @method void addCData($text)
 */
class SimpleXMLWrapper 
{

	/** @var null|SimpleXMLElement */
	protected $xml = null;


	public function __construct($data, $options = 0, $data_is_url = false, $ns = "", $is_prefix = false)
	{
		if($data instanceof SimpleXMLElement) {
			$this->xml = new SimpleXMLElementExtended($data->asXML());
		}
		else {
			$this->xml = new SimpleXMLElementExtended($data, $options, $data_is_url, $ns, $is_prefix);
		}
	}


	/**
	 * @return static
	 */
	public static function createFromFile($data, $options = 0, $ns = "", $is_prefix = false)
	{
		return new static($data, $options, true, $ns, $is_prefix);
	}


	/**
	 * @return static
	 */
	public static function create($data, $options = 0, $data_is_url = false, $ns = "", $is_prefix = false)
	{
		return new static($data, $options, $data_is_url, $ns, $is_prefix);
	}


	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->xml, $name), $arguments);
	}

	public function __get($name)
	{
		return new static($this->xml->$name);
	}


	/**
	 * Returns an array of SimpleXMLWrapper based on xpath result from xml
	 * @return static[] or false
	 */
	public function xpath($xpath)
	{
		$result = $this->xml->xpath($xpath);

		if($result === false) {
			return false;
		}

		$result = $this->wrapSimpleXMLArray($result);

		return $result;
	}


	/**
	 * Returns SimpleXMLWrapper of xml children
	 * @return static[]
	 */
	public function children($ns = null, $is_prefix = false)
	{
		$children = $this->xml->children($ns, $is_prefix);

		return $this->wrapSimpleXMLArray($children);
	}


	/**
	 * @return SimpleXMLWrapper
	 */
	public function child($name)
	{
		return static::create($this->xml->$name);
	}


	/**
	 * @return bool
	 */
	public function hasChild($name)
	{
		return isset($this->xml->$name) > 0 ? true : false;
	}


	/**
	 * @return string
	 */
	public function toString($silent = false, $trim = true)
	{
		if(!$silent) {
			static::assertLeafNode($this->xml);
		}
		$stringValue = (string)$this->xml;

		if($trim) {
			$stringValue = trim($stringValue);
		}
		return $stringValue;
	}


	/**
	 * @return float
	 */
	public function toFloat($silent = false)
	{
		$stringValue = str_replace(',', '.', $this->toString($silent));

		return (float)$stringValue;
	}


	/**
	 * @return bool
	 */
	public function toBoolean($silent = false)
	{
		$stringValue = $this->toString($silent);

		$boolValue = true;

		if(
			$stringValue == ''
			||
			$stringValue == 'false'
			||
			$stringValue == '0'
		) {
			$boolValue = false;
		}

		return $boolValue;
	}


	/**
	 * @return stdClass
	 */
	public function toStdClass()
	{
		return json_decode(json_encode($this->xml));
	}


	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->simpleXMLToArray($this->xml);
	}


	/**
	 * @return array
	 */
	public function simpleXMLToArray($data)
	{
		$array = (array)$data;
		
		foreach ($array as $key => $value){

			if( (is_object($value) && strpos(get_class($value),"SimpleXML")!==false) || is_array($value)) {

				$array[$key] = $this->simpleXMLToArray($value);
			}
		}

		return $array;
	}


	/**
	 * @return array
	 */
	public static function wrapSimpleXMLArray($xmlArray)
	{
		if(!is_array($xmlArray)) {
			$xmlArray = array($xmlArray);
		}

		$out = array();
		foreach ($xmlArray as $element) {
			$out[] = static::create($element);
		}

		return $out;
	}


	/**
	 * @throws WrongArgumentException
	 */
	public static function assertLeafNode(SimpleXMLElement $xml)
	{
		if(count($xml->children())) {
			throw new WrongArgumentException($xml->getName() . ' is not a leaf node!');
		}
	}


	/**
	 * @return string
	 */
	public function toXML()
	{
		$xml=dom_import_simplexml($this->xml);
		$xml->ownerDocument->encoding = 'UTF-8';
		return $xml->ownerDocument->saveXML();
	}
}