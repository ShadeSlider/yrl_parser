<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
include_once './config.inc.php';


$offerProcessor = 
	RealtyOfferListXMLProcessor::create()->
	openFile(DATA_DIR . 'test.xml')
;

$offerDAO = new RealtyOfferDAO();

$insertedOffersCount = 0;
while($offerProcessor->readNextOffer()) {
	if(!$offerProcessor->isCurrentOfferValid()) {
		continue;
	}
	
	$offerData = $offerProcessor->getCurrentOfferData();
	
	try {
		if(!$offerDAO->checkExistenceBy('internal_id', $offerData['internal_id'])) {
			$offerDAO->insertRecordFromArray($offerData);
			$insertedOffersCount++;
		}
		else {
			print("Offer already exists!\n\n");	
		}
	}
	catch (Exception $e) {
		print("Could not insert offer record!\n\n");
	}
}

print("\n$insertedOffersCount offers parsed and inserted!\n\n");