<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class RealtyOfferDAO 
{
	/** @var MysqlDBDriver */
	private $db;
	
	private $mainTable = 'realty_offer';
	private $locationTable = 'realty_offer_location';
	private $salesAgentTable = 'realty_sales_agent';
	private $imageTable = 'realty_offer_image';
	
	public function __construct($db = null)
	{
		if($db === null) {
			$this->db = MysqlDBDriver::getDefaultInstance();
		}
		else {
			$this->db = $db;
		}
	}

	/**
	 * @return $this
	 */
	public function insertRecordFromArray($data)
	{
		//Price
		$data['price_currency'] = $data['price']['currency'];
		$data['price_value'] = $data['price']['value'];
		if(isset($data['price']['period'])) {
			$data['price_period'] = $data['price']['period'];	
		}
		if(isset($data['price']['unit'])) {
			$data['price_unit'] = $data['price']['unit'];
		}
		unset($data['price']);

		//Area
		if(isset($data['area'])) {
			$data['area_value'] = $data['area']['value'];
			$data['area_unit'] = $data['area']['unit'];
			unset($data['area']);			
		}
		
		//Lot Area
		if(isset($data['lot-area'])) {
			$data['lot_area_value'] = $data['lot-area']['value'];
			$data['lot_area_unit'] = $data['lot-area']['unit'];
			unset($data['lot-area']);			
		}

		//Living Space
		if(isset($data['living-space'])) {
			$data['living_space_value'] = $data['living-space']['value'];
			$data['living_space_unit'] = $data['living-space']['unit'];
			unset($data['living-space']);			
		}

		//Kitchen Space
		if(isset($data['kitchen-space'])) {
			$data['kitchen_space_value'] = $data['kitchen-space']['value'];
			$data['kitchen_space_unit'] = $data['kitchen-space']['unit'];
			unset($data['kitchen-space']);			
		}
		
		$offerLocation = $data['location'];
		unset($data['location']);
		if(isset($offerLocation['metro'])) {
			$offerLocation['metro_name'] = $offerLocation['metro']['name']; 
			$offerLocation['metro_time_on_foot'] = $offerLocation['metro']['time-on-foot']; 
			if(isset($offerLocation['metro']['time-on-transport'])) {
				$offerLocation['metro_time_on_transport'] = $offerLocation['metro']['time-on-transport'];
			}
			unset($offerLocation['metro']);
		}
		
		
		$offerSalesAgent = $data['sales-agent'];
		unset($data['sales-agent']);

		$offerImages = array();
		if(isset($data['image'])) {
			$offerImages = (array)$data['image'];
			unset($data['image']);
		}

		
		
		$offerLocation = $this->escapeArrayValues($this->normalizeArrayKeys($offerLocation));
		$offerSalesAgent = $this->escapeArrayValues($this->normalizeArrayKeys($offerSalesAgent));
		$offerImages = $this->escapeArrayValues($this->normalizeArrayKeys($offerImages));
		$data = $this->escapeArrayValues($this->normalizeArrayKeys($data));

		$this->db->begin();
		
		$salesAgentId = $this->insertOrGetIdForSalesAgent($offerSalesAgent);
		
		$data['agent_id'] = $salesAgentId;
		$offerId = $this->db->insertRow($this->mainTable, $data);
	
		$offerLocation['offer_id'] = $offerId;
		$this->db->insertRow($this->locationTable, $offerLocation);
		
		array_walk($offerImages, function (&$imageUrl, $idx, $offerId) {
			$imageData = array('offer_id' => $offerId, 'url' => $imageUrl);
			$this->db->insertRow($this->imageTable, $imageData);	
		}, $offerId);
		
		
		$this->db->commit();
		

		return $this;
	}

	/**
	 * @return bool
	 */
	public function checkExistenceBy($fieldName, $value)
	{
		return $this->db->checkExistenceBy($this->mainTable, $fieldName, $value);
	}

	/**
	 * @return array
	 */
	private function normalizeArrayKeys($arr)
	{
		$result = array();
		foreach ($arr as $key => $value) {
			$result[str_replace('-', '_', $key)] = $value;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	private function escapeArrayValues(&$arr)
	{
		array_walk($arr, function (&$value) {
			$value = htmlentities($value, ENT_NOQUOTES);	
		});
		
		return $arr;
	}


	/**
	 * @return int
	 */
	private function insertOrGetIdForSalesAgent($salesAgentData)
	{
		$existingId = $this->db->getIdBy($this->salesAgentTable, 'phone', $salesAgentData['phone']);
		
		if($existingId === null && isset($salesAgentData['email'])) {
			$existingId = $this->db->getIdBy($this->salesAgentTable, 'email', $salesAgentData['email']);	
		}
		
		if($existingId !== null) {
			return $existingId;
		}
		
		return $this->db->insertRow($this->salesAgentTable, $salesAgentData);
	}
}