<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class SimpleXMLElementExtended extends SimpleXMLElement 
{
	/**
	 * Adds a child with $value inside CDATA
	 */
	public function addChildWithCDATA($name, $value = NULL) {
		$newChild = $this->addChild($name);

		if ($newChild !== NULL) {
			$node = dom_import_simplexml($newChild);
			$no   = $node->ownerDocument;
			$node->appendChild($no->createCDATASection($value));
		}

		return $newChild;
	}


	public function addCData($cdataText) {
		$node = dom_import_simplexml($this);
		$no   = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($cdataText));
	}

	public function addXMLChild($appendant)
	{
		if(!$appendant instanceof SimpleXMLElement) {
			$appendant = new SimpleXMLElementExtended($appendant);
		}

		$dom = dom_import_simplexml($this);
		$fromDom = dom_import_simplexml($appendant);
		$dom->appendChild($dom->ownerDocument->importNode($fromDom, true));
	}
}

function sxml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
	$toDom = dom_import_simplexml($to);
	$fromDom = dom_import_simplexml($from);
	$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}