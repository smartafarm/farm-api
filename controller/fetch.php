<?php
class fetch extends controller{
	protected $bearer;
	function __construct() {
		
		$request = new request();
		if($request->checkReq())
		{
			$this->bearer = $_SERVER['HTTP_BEARER'];
		}
	}
	
	public function getDevices(){
		/*
		 * get the devices and readings of the deivces
		 * responds array of Json
		 */
		
		$reqBearer = $this->bearer;		
		$this->model->getDevices($reqBearer);
	}

	public function getUpdate(){
		// Triggers a notification if new reading has been added.
		// responds JSON data of reading to ajax poll.
		$reqBearer = $this->bearer;	
		
		$this->model->getUpdate($reqBearer);
		
	}
	public function getrawdata(){
		// Triggers a notification if new reading has been added.
		// responds JSON data of reading to ajax poll.
		
		
		$this->model->getrawdata();
		
	}
	public function getuserinfo($data){
	// Triggers a notification if new reading has been added.
	// responds JSON data of reading to ajax poll.
	
	
	$this->model->getuserinfo($data);
	
}
	public function readings($data){
		// Triggers a notification if new reading has been added.
		// responds JSON data of reading to ajax poll.
		if($data =='device'){
			$did = $_GET['did'];
			$this->model->readings($data,$did);
		}
		if($data =='deviceInfo'){
			$did = $_GET['did'];
			$this->model->readings($data,$did);
		}
		//$this->model->readings($data);
		
	}
	
	
}