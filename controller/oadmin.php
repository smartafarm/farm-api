<?php
/*
 @desc - Organisation Admin Managment Class
 */
class oadmin extends controller{
	protected $bearer;
	function __construct() {
		/*
		Checking request to the server and token values
		Setting headers for each request
		*/
		$request = new request();
		if($request->checkReq(true,false,true))
		{
			$this->bearer = $_SERVER['HTTP_BEARER'];
		}
	}

	public function createUser(){
		/*
		 * creating organisation user
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->createUser($data);

		}
	
	public function getUsers(){
		// gets all user for organisation administration
		$reqBearer = $this->bearer;	
		$this->model->getUsers($reqBearer);
		
	}

	public function getorg($data){
		// gets all user for administration
		$reqBearer = $this->bearer;	
		$this->model->getorg($reqBearer,$data);
		
	}
	public function getDeviceFunc(){
		/*
		 * gets generic device function
		 */
		$this->model->getDeviceFunc();
	}
	public function getoadminDevices($data){
		/*
		 * gets generic device function
		 */
		$this->model->getoadminDevices($data);
	}
	public function setDeviceAccess(){
		/*
		 * sets the device access for individual user
		 */
		$data = json_decode(file_get_contents('php://input'), true);
		$this->model->setDeviceAccess($data);

		}
	
}
?>
