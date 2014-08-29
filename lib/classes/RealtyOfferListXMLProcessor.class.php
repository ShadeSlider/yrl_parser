<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class RealtyOfferListXMLProcessor 
{
	/** @var  XMLReader */
	private $xmlReader;
	
	public function __construct()
	{
		$this->xmlReader = new XMLReader();
	}

	public static function create()
	{
		return new static;
	}
	
	public function openFile($fileName)
	{
		if(!$this->xmlReader->open($fileName)) {
			throw new Exception('Cannot open file "' . $fileName . '"');
		}
		
		while($this->xmlReader->name != 'offer') {
			$this->xmlReader->read();
		}
		
		return $this;
	}


	public function readNextOffer()
	{
		$reader = $this->xmlReader;
		$reader->next('offer');
		
		if($reader->nodeType == XMLReader::NONE) {
			return false;
		}
		
		return true;
	}


	public function isCurrentOfferValid()
	{
		$domDocument = $this->getCurrentOfferDomDocument();
		libxml_use_internal_errors(true);
		$validationResult = $domDocument->schemaValidate(DATA_DIR . 'single_offer.xsd');

		if(!$validationResult) {
			return false;
		}
		
		return true;
	}


	public function getCurrentOfferData()
	{
		$domDocument = $this->getCurrentOfferDomDocument();
		
		$element = new SimpleXMLWrapper($domDocument->saveXML(), LIBXML_NOCDATA);

		$dataArray = $element->toArray();
		$dataArray['internal_id'] = $dataArray['@attributes']['internal-id'];
		unset($dataArray['@attributes']);
		
		return $dataArray;
	}


	public function insertCurrentOfferRecord()
	{
		
	}

	/**
	 * @return DOMDocument
	 */
	protected function getCurrentOfferDomDocument()
	{
		$domNode = $this->xmlReader->expand();
		$domDocument = new DOMDocument();
		$domDocument->appendChild($domNode);
		return $domDocument;
	}
}