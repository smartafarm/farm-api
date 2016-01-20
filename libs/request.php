<?php
/*
@Class - request
@desc - handels server request and authenticates pre flight
 */
class request {
    public function __construct() {     
        $dbobj = new database();
        $this->db = $dbobj->get();
        $this->session = new session($this->db);        
    }
    public function checkReq($checkToken = true,$checkAdmin = false,$checkoadmin = false)
    {
            
    
    		if($_SERVER['REQUEST_METHOD'] == "GET")
        	{
                // Setting accepted headers for get request
        	    header('Access-Control-Allow-Origin: http://smartafarm.com.au'); 
        	    header('Access-Control-Allow-Headers:accept,bearer,x-auth-token');
                if($checkToken) {
                       if($this->session->tokenCheck($_SERVER,$checkAdmin,$checkoadmin)){                                      
                           return true;
                       }
                     } 	
               
        	}
            elseif($_SERVER['REQUEST_METHOD'] == "OPTIONS")
            {
                // Tell the Client we support invocations from 
               /* if($_SERVER['HTTP_ORIGIN'] == "http://localhost" || $_SERVER['HTTP_ORIGIN'] == "http://www.smartafarm.com.au")
                {*/
                //Preflight -- CORS// header expectation from any request
                header('Access-Control-Allow-Origin: http://smartafarm.com.au');
                header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
                header('Access-Control-Allow-Headers: accept,bearer,x-auth-token');
                header('Access-Control-Max-Age: 1728000');
                header("Content-Length: 0");
                header("Content-Type: application/json");              
                
               // }
               /* else
                {
                header("HTTP/1.1 403 Access Forbidden");
                header("Content-Type: text/plain");
                echo "You cannot repeat this request";
               
                }*/
            }
            elseif($_SERVER['REQUEST_METHOD'] == "POST")
            {
                /*
                // Tell the Client we support invocations from 
                 */ 
                
               /* if($_SERVER['HTTP_ORIGIN'] == "http://smartafarm.com.au" || $_SERVER['HTTP_ORIGIN'] == "http://localhost")
                {*/
                        // Header expectation from POST request
                        header('Access-Control-Allow-Origin: http://smartafarm.com.au');
                        //header('Access-Control-Allow-Origin: *');                        
                        if($checkToken) {
                           if($this->session->tokenCheck($_SERVER,$checkAdmin,$checkoadmin)){               
                               return true;
                           }
                        } 
               /* }
                else{
                    die("POSTing Only Allowed from http://smartafarm.com.au");
                }*/
            }
        else{
            die("No Other Methods Allowed");
        }

        //Token check from user
        //Uses Session class
        
               
        }

            
}
?>
