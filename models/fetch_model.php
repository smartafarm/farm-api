<?php
error_reporting(E_ALL ^ E_NOTICE);
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
			array_push($dAccess, $value['_id'][0]);
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
			$data["sensor"]	=  $device["sensor"];			
			foreach($alldAccess as $key=> $value)
			{
				if ($value['_id'][0] == $device["_id"])
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
				$data["readings"][$index]["dt"] = date(DATE_ISO8601, $data["readings"][$index]["dt"]->sec);
				$index++;
			}
			array_push($result,$data);	
		
		}
		// updates the timestamp for users last reading
		if(!$this->session->getTimestamp($bearer)){
			$this->session->setTimestamp($bearer, date('c'));
		}
		header('Content-Type: application/json');
		echo json_encode( $result , JSON_PRETTY_PRINT);
	}
	
	function getUpdate($timestamp,$bearer) {
	
		/*
		 *
		 * Helper function to the ajax poll request, responds if any new data is posted
		 * Json array returned
		 * @var - $timestamp - timestamp of the request made by user recevied from controller
		 *      - $bearer - user data received from the controller 
		 */
		
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
			array_push($dAccess, $value['_id'][0]);
		}	
		// last timestamp of user accessed the reading
		$lastReadings = $this->session->getTimestamp($bearer);
		$bearerLastReading = new MongoDate(strtotime($lastReadings));
		
		
		// current request time stamp
		$time =  new MongoDate(strtotime($timestamp));
			
		$collection = $this->db->deviceData;			
		// getting readings of each device from last time stamp of user
		// and user request time stamp
		$condition = array('dt' => array(
			'$gte'=>$bearerLastReading,
			'$lte'=>$time
			) ,
		// only for the accessible devices
		'did'=>array('$in' => $dAccess)
		);	
		$readings = $collection->find($condition);
		$result = Array();
		$result["readings"] = array();	
		$index = 0;		
		foreach ( $readings as $id => $value )
		{
			array_push($result["readings"], $value);
			// replacing ISO date from mongo date for front end
			$result["readings"][$index]["dt"] = date(DATE_ISO8601, $result["readings"][$index]["dt"]->sec);
			$index++;			
		}
		// updating last reading timestamp for user				
		$this->session->setTimestamp($bearer, $timestamp);
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
		$response = $collection->find();	
		$result =[];
		foreach ($response as $key => $value) {
			array_push($result, $value['msg']);
		}

		header('Content-Type: application/json');
		echo json_encode($result,JSON_PRETTY_PRINT);
	}
}	
//1445297068211 
//1445297090211