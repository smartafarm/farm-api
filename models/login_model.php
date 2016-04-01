<?php
/*
 * @desc - Login model
 * @author - Vandish Gandhi
 * @Version Control:
 * 1.0 - Authenticates the login and creates session
 *
 */
use \Firebase\JWT\JWT;
error_reporting(E_ALL ^ E_NOTICE);
class login_model extends Model {
	function __construct(database $database) {
		parent::__construct();
	} // end of construct
	
	public function check($data) {
		/*
		@var - $data - user credentials recevived from controller
	 	*/
		
		$collection = $this->db->userMaster;
		$authenticate = array (
				"uname" => $data['credentials']['username'],
				"password" => $data['credentials']['password'] 				
		);		

		$result = $collection->count ($authenticate);
		$setDt = new DateTime();
		if ($result == 1) {
		// if credentials are true
		$readings = $collection->find($authenticate)	;
		
		foreach ($readings as $key => $reading) {		
		$username = $data['credentials']['username'];
		$set = array(
			"timestamp" => time(),
		    "user" => $username		    
		);
		// creating a JSON web TOKEN
		$jwt = JWT::encode($set, TOKEN_KEY);	
		
 		if (!$this->session->getToken($username))
 		{
 			//create session for user 			 			
 			$this->session->setToken($username,$jwt,$reading['details']['type']);					
 		}
 		$token = $this->session->getToken($username);
 		
 		// sign token to forward on client side
 		$response = array(
 			'token' => $token,
 			'id'	=> $username,
 			'role'	=> $reading['user']['role'],
 			'details' => $reading['details']
 			 );
 		header('Content-Type: application/json');
 		echo json_encode($response);					
		 	}		
		 } 
	 else
	 {
		// if user not found	 	
	 	$data = array();
	 	$data["response"]  = "Invalid Login";			
 		http_response_code(401);
 		header('Content-Type: application/json');
	 	echo json_encode( $data , JSON_PRETTY_PRINT);		
	 }
} // end of check


public function validate($data){
	// validating user token 
	// currently un used as preflight request already validates token
	print_r($_SESSION)	;
	if ($this->session->get($data['data']['user'])){
		$gettoken= $this->session->get($data['data']['user']);
			if ($data['data']['token'] == $gettoken['token']){
				http_response_code(200);		
			}else{
				unset($_SESSION[$data['data']['user']]);
				http_response_code(401);	
			}
	}else{
		http_response_code(401);
	} // eof validate

	}
public function destroy($data){
	// destroy user session and token
	$this->session->destroy($data);

}
public function forgot($action,$data){
	// finsing id
	
	$collection = $this->db->userMaster;
	$email = '';
	$_id = '';
	$uname = '';
	$response = '';
	if($action == 'exists'){
		$result = $collection->find(array('uname' => $data['user']));
		$response  = $result->count() ;
		if($result->count() == 0){
			$result = $collection->find(array('details.email' => $data['user']));	
			$response = $result->count() ;		
		}
	//resetting the password
	if($result->count() == 1){
		foreach($result as $key=> $value)
		{	
			$_id	= $value['_id'];
			$uname =  $value['uname'];
			$email 	= $value['details']['email'];
		}	
		$update = $collection->update(
			    array('_id' => $_id),
			    array(
			        '$set' => array("password" => "default123" )
			        ),
			    array("upsert" => false)
			);
		if($update['n'] == 1){
			$sendmessage = '<p>Your request to reset password was successful.</p><br>';
			$sendmessage .= '<p>Your username : <strong>'. $uname .'</strong></p><br>';
			$sendmessage .= '<p>Your password : <strong>default123</strong></p><br>';
			//print_r($sendmessage);
			$this->email->send('vandish.gandhi@uts.edu.au',$sendmessage,'Password Recovery');
		}
	}
	
	}
	header('Content-Type: application/json');
	echo json_encode($response, JSON_PRETTY_PRINT);

}
}
