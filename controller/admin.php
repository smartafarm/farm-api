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
	public function getorg(){
		// gets all user for administration
		
		$this->model->getorg();
		
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
	}
	public function setDeviceAccess(){
		/*
		 * sets the device access for individual user
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->setDeviceAccess($data);

		}
	

	
}