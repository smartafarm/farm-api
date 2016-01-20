<?php
/*
 * @desc - Dashboard controller
 * @author - Vandish Gandhi
 * @Version Control:
 * 1.0 - Main Login controller. Authentication not implemented.   * 
 * 1.1 - Server preflight and cors request update.
 */
class login extends controller{
	function __construct() {
		parent::__construct(false);
		/*
		Checking request to the server and token values
		Setting headers for each request
		*/
		$request = new request();
		$request->checkReq(false);
		}
	public function index(){
		$this->view->render('login/index',false);
	}

	public function authenticate(){
		/*
		 * responds to the login credintials
		 */
		
		$data = json_decode(file_get_contents('php://input'), true);		
		$check = $this->model->check($data);
		
	}
	public function validate(){
		/*
		 * Validates the token 
		 */
		$data = json_decode(file_get_contents('php://input'), true);		
		$check = $this->model->validate($data);
		
	}
	public function destroy(){
		/*
		 *destroys the token
		 */
		$data = json_decode(file_get_contents('php://input'), true);		
		$check = $this->model->destroy($data);
		
	}
	public function forgot($action){
		/*
		 *destroys the token
		 */
		$data = json_decode(file_get_contents('php://input'), true);		
		$check = $this->model->forgot($action,$data);
		
	}
}