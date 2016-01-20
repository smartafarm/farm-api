<?php
/*
 @desc - Admin Managment Class
 */
class admin extends controller{
	protected $bearer;
	function __construct() {
		/*
		Checking request to the server and token values
		Setting headers for each request
		*/
		$request = new request();
		$request->checkReq(true,true);
	}
	
	public function createUser(){
		/*
		 * creating organisation admin
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->createUser($data);

		}
	public function createOrg(){
		/*
		 * creating organisation admin
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->createOrg($data);

		}		
	public function getorg($data){
		// gets all user for administration
		
		$this->model->getorg($data);
		
	}		
	public function getUsers(){
		// gets all user for administration
		
		$this->model->getUsers();
		
	}
	public function getAllDevices(){
		/*
		 * gets all devices for user administration
		 */
		$this->model->getAllDevices();
	}
	public function getDeviceFunc(){
		/*
		 * gets generic device function
		 */
		$this->model->getDeviceFunc();
	}public function setDeviceAccess(){
		/*
		 * sets the device access for individual user
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->setDeviceAccess($data);

		}
	
	public function setOrgDeviceAccess(){
		/*
		 * sets the device access for individual user
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->setOrgDeviceAccess($data);

		}
	public function getDevices($data){
	/*
	 * sets the device access for organisation
	 */
	
	$this->model->getDevices($data);

	}

	public function check($data){
	/*
	 * sets the device access for organisation
	 */
	//getting id
	if($data=='assetid'){
		$did = $_GET['did'];	//device id
		$aid = $_GET['aid'];	//assetid
		$sid = $_GET['sid']	;//sensorid
		$this->model->check($data,$did,$aid,$sid);
	}elseif($data=='sensor'){
		$did = $_GET['did'];
		$aid = $_GET['aid'];	//assetid
		$sid = $_GET['sid']	;//sensorid
		$this->model->check($data,$did,false,$sid);
	}
	elseif($data=='device'){
		$did = $_GET['did'];
			;//sensorid
		$this->model->check($data,$did,false,false);
	}
	}
	public function update($action){
	/*
	 * sets the device access for organisation
	 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->update($action,$data);
	}	
	public function addDevice(){
	/*
	 * sets the device access for organisation
	 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->addDevice($data);
	}	

	
}