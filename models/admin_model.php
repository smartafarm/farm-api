<?php
/*
 * @desc - Dashboard Model
 * @author - Vandish Gandhi
 * @Version Control:
 * 1.0 - Base model class , get passes the database instance
 * 1.1 basic backend administration model created
 */
error_reporting(E_ERROR | E_PARSE);

class admin_model extends Model{
	function __construct(database $database) {
		// getting the base properties for the parent model
		parent::__construct();

		
	}
	
	public function createUser($data){
		/*
		Creating Administrating user for the organisation
		 */
		$collection = $this->db->userMaster;		
		//setting default password
		$data['serverData']['password'] = "default123";
		$data['serverData']['device'] = new stdClass();
		$data['serverData']['genFunc'] = new stdClass();
		$response = $collection->insert($data['serverData']);
		if($response['ok'] == 0){
			//if no data is appended
			http_response_code(202);
		}		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}
		public function addDevice($data){
		/*
		Adding a new device
		 */
		$collection = $this->db->DeviceMaster;				
		$data['serverData']['_id']	= $data['serverData']['id'];
		unset($data['serverData']['id']);
		$data['serverData']['Description']	= $data['serverData']['desc'];
		unset($data['serverData']['desc']);
		$response = $collection->insert($data['serverData']);
		if($response['ok'] == 0){
			//if no data is appended
			http_response_code(202);
		}	
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}
	public function getUsers() {

	/*
	 // responds to get request for all users
	 */	
	
		$collection = $this->db->userMaster;
		$readings = $collection->find(array('uname' => array('$ne' => 'admin')));
		$result = Array();
		$result["users"] = array();	
		$index = 0;		
		foreach ( $readings as $id => $value )
		{
			$data = array();
			$data['uname'] = $value['uname'];
			$data['details']=$value['details'];
			$data['device'] = $value['device'];
			$data['genFunc'] = $value['genFunc'];
			array_push($result["users"], $data);			

		}				
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	public function getDevices($data) {

	/*
	 * gets all devices for particular user
	 */	
		
		$collection = $this->db->orgMaster;
		// only controller devices sent
		$readings = $collection->find(array('name' => $data));
		
		$result = array();	
		
		foreach ( $readings as $id => $org )
		{			
			$devices =  $org['device'];	
		}

		foreach ( $devices as $id => $device )
		{			
		$collection = $this->db->DeviceMaster;
		// only controller devices sent
		$readings = $collection->find(array("_id" => $id));
		
		
		
			foreach ( $readings as $id => $device )
			{	
				$data=array();
				$data["_id"] = $device["_id"];
				$data["name"]	=  $device["EquipName"];
				$data["Desc"]	=  $device["Description"];
				$data["Status"]	=  $device["status"];				
				array_push($result, $data);	

			}			
		}				
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}



	public function getAllDevices() {

	/*
	 * gets all devices from database
	 */	
	
		$collection = $this->db->DeviceMaster;
		// only controller devices sent
		$readings = $collection->find(array('EquipTypeID' => 'c1'))->sort(array("_id" => 1));
		
		$result = array();	
		
		foreach ( $readings as $id => $device )
		{
			$data["_id"] = $device["_id"];
			$data["name"]	=  $device["EquipName"];
			$data["Desc"]	=  $device["Description"];
			$data["Status"]	=  $device["status"];
			$data["sensor"]	=  $device["sensor"];
			$data["asset"]	=  $device["asset"];
			array_push($result, $data);	

		}				
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	public function getDeviceFunc() {

	/*
	 * gets all the general device functions
	 */	
	
		$collection = $this->db->functionMaster;
		$readings = $collection->find(array('type' => 'Device'));
		$result = array();	
		
		foreach ( $readings as $id => $function )
		{
			
			array_push($result, $function);	

		}				
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	public function setDeviceAccess($data) {
	
	/*
	 * sets individaul device access
	 * @var - $data - recevied from controller with username and deviceAccess array
	 */	
		$collection = $this->db->userMaster;
		//print_r($data['serverData']);
		$username = $data['serverData']['uname'];		
		$access = $data['serverData']['dAccess'];
		
		$response =$collection->update(
		    array('uname' => $username),
		    array(
		        '$set' => array("device" => $access),
		    ),
		    array("upsert" => false)
		);
		
		if($response['n'] == 0){
			// if no record updated
			http_response_code(202);
		}		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
		
	}
	public function setOrgDeviceAccess($data) {
	
	/*
	 * sets individaul device access
	 * @var - $data - recevied from controller with username and deviceAccess array
	 */	
		$collection = $this->db->orgMaster;
		//print_r($data['serverData']);
		$oname = $data['serverData']['oname'];		
		$access = $data['serverData']['dAccess'];
		
		$response =$collection->update(
		    array('name' => $oname),
		    array(
		        '$set' => array("device" => $access),
		    ),
		    array("upsert" => false)
		);
		
		if($response['n'] == 0){
			// if no record updated
			http_response_code(202);
		}		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
		
	}
	public function createOrg($data){
		/*
		Creating organisation
		 */
		$collection = $this->db->orgMaster;		
		//setting default password
		$data['serverData']['device'] = new stdClass();
		$response = $collection->insert($data['serverData']);
		if($response['ok'] == 0){
			//if no data is appended
			http_response_code(202);
		}		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}

	public function getorg($data) {

	/*
	 // responds to get request for all users
	 */	
	
		$collection = $this->db->orgMaster;
		if($data == 'addUser'){
			//get or name only
			$readings = $collection->find();
			$result = Array();		
			foreach ( $readings as $id => $value )
			{
				unset($value[_id]);
				unset($value[device]);
				unset($value[cperson]);
				unset($value[email]);



				array_push($result, $value);			

			}	
		}
		else{
			//getting all properties of org
			$readings = $collection->find();
			$result = Array();		
			foreach ( $readings as $id => $value )
			{
				unset($value[_id]);
				array_push($result, $value);			
	
			}	
			}			
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	public function check($action,$did,$aid=false,$sid=false) {

	/*
	 // responds to get request for all users
	 */	
	if($action == 'assetid'){
		$collection = $this->db->DeviceMaster;
		$response = $collection->count (array('_id'=>$did,'asset.id'=>$aid));	
		$result = array();
		$result['aid'] = $response;
		$response = $collection->count (array('_id'=>$did,'asset.sensor'=>$sid));	
		$result['sid'] = $response;
	}elseif($action == 'sensor')
	{
		$result = array();
		$collection = $this->db->DeviceMaster;		
		$response = $collection->count (array('_id'=>$did,'asset.sensor'=>$sid));	
		$result['sid'] = $response;
	}
	elseif($action == 'device')
	{
		$result = array();
		$collection = $this->db->DeviceMaster;		
		$response = $collection->count (array('_id'=>$did));	
		$result['did'] = $response;
	}
		
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}

	public function update($action,$data){
		/*
		updates tables based on actions
		 */
		
		if($action == 'newasset'){
			$collection = $this->db->DeviceMaster;		
		//setting default password
			
			$did = $data['serverData']['did'] ;
			$asset = $data['serverData']['asset'] ;
			$response = $collection->update(array('_id'=>$did),array('$push'=>array( 'asset'=> $asset)));
		}
			if($action == 'sensor'){
			$collection = $this->db->DeviceMaster;		
		//setting default password
		
			$did = $data['serverData']['did'] ;
			$sid = $data['serverData']['sid'] ;
			$newsensor = $data['serverData']['addsensor'] ;			
			//updating sensor based on index found
			$response = $collection->update(array('_id'=>$did,'asset.id'=>$sid),array('$push'=>array( 'asset.$.sensor'=> $newsensor)));
		}

		
		if($response['ok'] == 0){
			//if no data is appended
			http_response_code(202);
		}		
		header('Content-Type: application/json');
		echo json_encode( $response, JSON_PRETTY_PRINT);
	}
	
}