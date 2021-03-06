<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
class fetch_model extends Model{
	function __construct(database $database) {
		// getting the base properties for the parent model
		parent::__construct();

	}


	function getDevices($bearer) {
		/*		 
		 * Gets all the devices in the dataBase based on the user
		 * @var - $bearer - user received from fetch controller  		 
		 */
		
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
		$collection1 = $this->db->deviceData;
		// getting users devices
		$devices = $collection->find(array('_id'=>array('$in' => $dAccess)));
		$result = Array();
		$result = array();
		$i=0;
		foreach ( $devices as $id => $device )
		{
			$data["_id"] = $device["_id"];
			$data["name"]	=  $device["EquipName"];
			$data["Desc"]	=  $device["Description"];
			$data["Status"]	=  $device["status"];
			//$data["sensor"]	=  $device["sensor"];			
			$data["asset"]	=  $device["asset"];
			foreach($alldAccess as $key=> $value)
			{
				if ($key == $device["_id"])
				{
					$data["func"] = $value['func'];
					
				}
			}	
			$data["readings"] = array();
			// appending all the device readings 
			$readings = $collection1->find(array('did' => $device["_id"]));
			$index = 0;
			foreach ($readings as $key => $reading) {
				/*
				JSON PROTOTYPE OF READING
				"_id": {
                    "$id": "5626da24c2677bcc14000029"
                },
                "did": "s123xyz",
                "s_count": 4,
                "lat": "lattitude",
                "long": "longitude",
                "dt": {
                    "sec": 1445386788,
                    "usec": 0
                },
                "T01": "1",
                "T02": "23",
                "L01": "35",
                "L02": "45"*/

				array_push($data["readings"],$reading);

				// converting date into ISO for Client from mongo date object
				//$data["readings"][$index]["dt"] = date(DATE_ISO8601, $data["readings"][$index]["dt"]->sec);
				$data["readings"][$index]["dt"] = date('m/d/Y H:i:s', $data["readings"][$index]["dt"]->sec);
				$index++;
			}
			array_push($result,$data);	
		
		}
		// updates the timestamp for users last reading
			$timestamp = date('dmYHis',$_SERVER['REQUEST_TIME']);
			$this->session->setTimestamp($bearer, $timestamp);
		
		header('Content-Type: application/json');
		echo json_encode( $result );
	}
	
	function getUpdate($bearer) {
	
		/*
		 *
		 * Helper function to the ajax poll request, responds if any new data is posted
		 * Json array returned
		 * @var - $timestamp - timestamp of the request made by user recevied from controller
		 *      - $bearer - user data received from the controller 
		 */
		if(!isset($_GET['t'])){
			exit;
		}
		if(!isset($_GET['did'])){
			exit;
		}
		$timestamp = $_GET['t'];
		$device = $_GET['did'];
		$dAccess = array();
		$userCollection = $this->db->userMaster;
		// gets all devices accessible to user
		$dAccessResponse = $userCollection->find(array('uname'=>$bearer), array("device"));	
		foreach($dAccessResponse as $key=> $value)
		{
			$alldAccess = $value['device'];

		}	
		// creating array for all devices and allowed functions
		foreach($alldAccess as $key=> $value)
		{
			
			array_push($dAccess, $key);
		}	
		
		// last timestamp of user accessed the reading
		//$lastReadings = $this->session->getTimestamp($bearer);
		$lastReadings=DateTime::createFromFormat('dmYHis', $this->session->getTimestamp($bearer));
		$fromTime = new MongoDate($lastReadings->getTimestamp());
		
		
		// current request time stamp
		$toTime = DateTime::createFromFormat('dmYHis',$timestamp);
		//$toTime = new DateTime($timestamp);
		
		$time =  new MongoDate($toTime->getTimestamp());
		/*print_r($lastReadings);
		print_r($toTime);
		print_r($fromTime);
		print_r($time);
		print_r($timestamp);
		print_r($device);
		print_r($dAccess);*/
		

		$collection = $this->db->deviceData;			
		// getting readings of each device from last time stamp of user
		// and user request time stamp
		$condition = array(
			
			'dt' => array(
			'$gte'=>$fromTime,
			'$lte'=>$time
			) ,
		// only for the accessible devices
		'did'=>array('$in'=>$dAccess)
		);	
		$hit = false;
		$readings = $collection->find($condition);
		//print_r($readings->count());
		$result = Array();
		$result["readings"] = array();	
		$index = 0;		
		foreach ( $readings as $id => $value )
		{
			$hit = true;
			array_push($result["readings"], $value);
			// replacing graph from mongo date for front end
			$result["readings"][$index]["dt"] = date('m/d/Y H:i:s', $result["readings"][$index]["dt"]->sec);
			$index++;			
		}
		// updating last reading timestamp for user	

		if($hit)
		{
			$timestamp = date('dmYHis',$_SERVER['REQUEST_TIME']);	
			$this->session->setTimestamp($bearer,$timestamp);
		}
		header('Content-Type: application/json');
		echo json_encode($result,JSON_PRETTY_PRINT);
	}
	function getrawdata() {
	
		/*
		 *
		 * Helper function to the ajax poll request, responds if any new data is posted
		 * Json array returned
		 * @var - $timestamp - timestamp of the request made by user recevied from controller
		 *      - $bearer - user data received from the controller 
		 */
		
		
		$collection = $this->db->rawMaster;
		$response = $collection->find()->sort(array('$natural' => -1) );	
		
		$result =[];
		foreach ($response as $key => $value) {
			array_push($result, $value['msg']);
		}

		header('Content-Type: application/json');
		echo json_encode($result,JSON_PRETTY_PRINT);
	}
	function getuserinfo($bearer) {
		/*		 
		 * Gets all the devices in the dataBase based on the user
		 * @var - $bearer - user received from fetch controller  		 
		 */
		
		
		$userCollection = $this->db->userMaster;
		$dAccessResponse = $userCollection->find(array('uname'=>$bearer));
		//gets all devices accessible to the user	
		$dAccessResponse->sort(array('device'=>1));
		foreach($dAccessResponse as $key=> $value)
		{
			
				$result = $value;

		}	
		unset($result['_id']);
		//unset($result['uname']);
		unset($result['password']);
		unset($result['genFunc']);
		header('Content-Type: application/json');
		echo json_encode( $result , JSON_PRETTY_PRINT);
	}
	function readings($data,$did) {
		/*		 
		 * Gets all the devices in the dataBase based on the user
		 * @var - $bearer - user received from fetch controller  		 
		 */
		if($data == 'device'){
			$collection = $this->db->deviceData;
			$response = $collection->find(array('did'=>$did));			
			$result = array();
			foreach($response as $key=> $value)
			{			
				unset($value['_id']);
				unset($value['lat']);
				unset($value['long']);
				$value['dt'] = date('m-d-Y H:i:s', $value['dt']->sec);
				array_push($result, $value);
			}	
			
		}

		if($data == 'deviceInfo'){
			$collection = $this->db->DeviceMaster;
			$response = $collection->find(array('_id'=>$did));			
			$result = array();			
			foreach($response as $key=> $value)
			{			
				
				
				unset($value['EquipTypeID']);								
				array_push($result, $value);
			}	
			
		}
		
		
		
		header('Content-Type: application/json');
		echo json_encode( $result , JSON_PRETTY_PRINT);
	}
		
}	
