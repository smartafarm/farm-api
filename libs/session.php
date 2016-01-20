<?php
/*
 * @desc - Session Library
 * @author - Vandish Gandhi
 * @Version Control:
 * 1.0 - Basic session creating functions
 *
 */
use \Firebase\JWT\JWT;
error_reporting(E_ALL ^ E_NOTICE);
class session {
	
	public function __construct($database) {	
		$this->db = $database;
	}
	public  function init()
	{
		@session_start();
		//$_SESSION["TOKENS"] =array();
	}
	public function set($key,$value){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		
		   $collection->update(
            array('_id' => $key),
            array($value),
            array('upsert' => false)
        );
		
	}
	public function setToken($key,$value,$role){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		
		   $collection->update(
            array('_id' => $key),
            array('$set'=>array('token'=>$value,'role'=>$role)),
            array('upsert' => true)
        );
		
	}

	public function setTimestamp($key,$value){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		
		   $collection->update(
            array('_id' => $key),
            array('$set'=>array('timestamp'=>$value)),
            array('upsert' => false)
        );
		
	}

	public function getToken($key){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		
		$result = $collection->find(array('_id' => $key));
		if($collection->count() > 0){
			foreach($result as $key1 => $value)
			{
				return $value['token'];
			}
			}else
			{
				return false;
			}
		
	}
	public function getRole($key){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		

		$result = $collection->find(array('_id' => $key));

		if($collection->count() > 0){
			foreach($result as $key1 => $value)
			{

				return $value['role'];
			}
			}else
			{
				return false;
			}
		
	}
	public function getTimestamp($key){
		// sets the value in Session Global as per the request by server
		$collection = $this->db->sessionMaster;		
		$result = $collection->find(array('_id' => $key));
		if($collection->count() > 0){
			foreach($result as $key1 => $value)
			{
				
				return $value['timestamp'];
			}
			}else
			{
				return false;
			}
		
	}
	public function get($key){
		$collection = $this->db->sessionMaster;		
		$result = $collection->find(array('_id' => $key));
		if($collection->count() > 0){
			foreach($result as $key1 => $value)
			{
				return $value;
			}
		}else
		{
			return false;
		}
		
	}
	
	public function destroy($key) {

		$collection = $this->db->sessionMaster;		
		$result = $collection->remove(array('_id' => $key['user']));
		print_r($result);
	}
	public function tokenCheck($request,$checkAdmin,$checkoadmin) {	
		
		// Checking token and beare from each request
		if(!isset($request['HTTP_BEARER']) || !isset($request['HTTP_X_AUTH_TOKEN'])){
		// If No Token			
	 	header("HTTP/1.1 401 No token or bearer");
	    header("Content-Type: text/plain");
	    echo "Access Denied";
	    die();
		}else{
		//check if request is admin specific			
		if($checkAdmin == true){
			if($request['HTTP_BEARER'] != 'admin'){
				header("HTTP/1.1 401 not admin");
			    header("Content-Type: text/plain");
			    echo "Access Denied";
			    die();
			}
		}	
		//check if request is organsation admin specific
		if($checkoadmin == true){
			$bearer = $request['HTTP_BEARER'] ;
			$role = self::getRole($bearer);
			
			if($role != 'oadmin' && $role != 'admin'){
				header("HTTP/1.1 401 Not authorized");
			    header("Content-Type: text/plain");
			    echo "Access Denied";
			    die();
			}
		
		}

		$bearer = $request['HTTP_BEARER'];
		$token = $request['HTTP_X_AUTH_TOKEN'];
		// Validate token against the PHP session
		
			if(!self::validate($token,$bearer)){
				header("HTTP/1.1 401 no session");
			    header("Content-Type: text/plain");
			    echo "Access Denied";
			    die();
			}else
			{
				// Respond true if all checks are ok
				return true;
			}
		}

	}
	public function validate($token,$bearer) {
		// checink token via JWT
		
		if(self::getToken($bearer) != $token)
		{
			return false;
		}else
		{
			//decoding token
			
			$decoded =(array) JWT::decode($token, TOKEN_KEY, array('HS256'));			
			$user = $decoded['user'];
			// checking session value of user
			$validation = self::get($user);
			
			
			if(!$validation) {
				return false;
			}elseif($validation['token'] != $token){
				return false;
			}
			else{
				//return true if the token matches with token stored in session after decode
				return true;
			}
		}

	}
}// end session class