<?php
/*
 * @desc - organisation admin Model
 * @author - Vandish Gandhi
 * @Version Control:
 * 1.0 -creating base model for organisation administration
 */
error_reporting(E_ERROR | E_PARSE);

class oadmin_model extends Model{
	function __construct(database $database) {
		// getting the base properties for the parent model
		parent::__construct();

		
	}
	
	public function createUser($data){
		
		//Creating Administrating user for the organisation
		 
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
	public function getUsers($bearer) {

	/*
	 // responds to get request for all users
	 */	
		
		$collection = $this->db->userMaster;
		$readings = $collection->find(array('uname' => $bearer));
		
		foreach ( $readings as $id => $value )
		{
			$org = $value['details']['bname'];			
		}	
		
		$readings = $collection->find(array('details.bname' => $org,'uname'=>array('$ne' => 'admin')));
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


	public function getorg($bearer,$data) {

	
	 // responds with organisation details
	 

	 	$collection = $this->db->userMaster;
		$readings = $collection->find(array('uname' => $bearer));
		
		foreach ( $readings as $id => $value )
		{
			// get admin organtisation details
			$org = $value['details']['bname'];			
		}	
	
	
		$collection = $this->db->orgMaster;
		if($data == 'info'){
			//get infor of organisation
			$readings = $collection->find(array('name'=>$org));
			$result = Array();		
			foreach ( $readings as $id => $value )
			{
				unset($value['_id']);
				unset($value['device']);
				unset($value['type']);
				//unset($value[cperson]);
				//unset($value[email]);



				array_push($result, $value);			

			}	
		}
	/*	else{
			//getting all properties of org
			$readings = $collection->find();
			$result = Array();		
			foreach ( $readings as $id => $value )
			{
				unset($value[_id]);
				array_push($result, $value);			
	
			}	
			}			
	*/
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	public function getDeviceFunc() {

	
	 // gets all the general device functions
	 	
	
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
	public function getoadminDevices($data) {

	
	 // gets all devices for particular user
	 	
		
		$collection = $this->db->userMaster;
		// only controller devices sent
		$readings = $collection->find(array('uname' => $data));
		
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
	public function setDeviceAccess($data) {
	
	
	 // sets individaul device access
	 // @var - $data - recevied from controller with username 
	 
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

}
	/*



	public function getAllDevices() {

	
	// gets all devices from database
	 
	
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
			array_push($result, $data);	

		}				
	
	header('Content-Type: application/json');
	echo json_encode($result,JSON_PRETTY_PRINT);
	}
	
	
	public function setOrgDeviceAccess($data) {
	
	
	 // sets individaul device access
	 // @var - $data - recevied from controller with username and deviceAccess array
	 
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
		
		//Creating organisation
		 
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
*/
	