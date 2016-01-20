<?php

class update_model extends Model{
	function __construct(database $database) {
		// getting the base properties for the parent model
		parent::__construct();

	}


	function deviceStatus($data) {
		/*
		@var - $data - revices device id and receives the device status
		 */
		$collection = $this->db->DeviceMaster;
		$collection->update(
		    array('_id' => $data['serverData']['_id']),
		    array(
		        '$set' => array("status" => $data['serverData']['status']),
		    ),
		    array("upsert" => false)
		);
		$response = $this->db->lastError();
		header('Content-Type: application/json');
		echo json_encode( $response['ok'], JSON_PRETTY_PRINT);
	}

		function fname($data) {
		/*
		@var - $data - revices device id and friendly name
		 */
		$collection = $this->db->DeviceMaster;		
		$response = $collection->update(
		    array('_id' => $data['serverData']['_id']),
		    array(
		        '$set' => array("EquipName" => $data['serverData']['fname'] )
		        ),
		    array("upsert" => false)
		);
		
		header('Content-Type: application/json');
		echo json_encode( $response['ok'], JSON_PRETTY_PRINT);
	}

	function sname($data) {
		/*
		@var - $data - revices device id and friendly name
		 */
		$collection = $this->db->DeviceMaster;		
		$response = $collection->update(
		    array('_id' => $data['serverData']['_id'],'asset.id'=>  $data['serverData']['asset']),
		    array(
		        '$set' => array(
	        	"asset.$.fname" => $data['serverData']['fname']
		        )
		   		 ),
		    array("upsert" => false)
		);
		 	
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}


		function checksensor($data) {
		/*
		@var - $data - revices device id and friendly name
		 */
		
		$collection = $this->db->DeviceMaster;		
		$result = $collection->find(
		    array('_id' => $data['serverData']['id'] , 'sensor.id' => $data['serverData']['sensor'])		    		        		    
		);
		if($result->count() == 0){
			$response = true;
		}else
		{
			$response=false;
		};

		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}

	
}	
