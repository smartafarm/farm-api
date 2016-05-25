<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class alerts_model extends Model{
	function __construct(database $database) {
		// getting the base properties for the parent model
		parent::__construct();

	}


	function getDevices($bearer) {
		$dAccess = array();
		$userCollection = $this->db->userMaster;
		$dAccessResponse = $userCollection->find(array('uname'=>$bearer), array("device"));
		//gets all devices accessible to the user	
		foreach($dAccessResponse as $key=> $value)
		{
			
				$alldAccess = $value['device'];

		}	
		//creating device and function array for response
		foreach($alldAccess as $key=> $value)
		{
			
			array_push($dAccess, $key);
		}	
		
		$collection = $this->db->DeviceMaster;
		
		// getting users devices
		$devices = $collection->find(array('_id'=>array('$in' => $dAccess)));
		$result = Array();
		
		
		foreach ( $devices as $id => $device )
		{

			array_push($result, $device);
		
		}	
		header('Content-Type: application/json');
		echo json_encode( $result , JSON_PRETTY_PRINT);
		}

		function getAlerts($did,$bearer) {
		
		$collection = $this->db->alerts;
		$response = $collection->find(array('uname'=>$bearer , 'did' => $did));
		//gets all alerts accessible to the user	
		$result = array();
		foreach($response as $key=> $value)
		{		

			array_push($result, $value);
		}			
		if($result){
		header('Content-Type: application/json');
		echo json_encode( $result , JSON_PRETTY_PRINT);
		}
		}
		
}	
