<?php 
/*
 * @desc - class to receive data from the devices
 *
 * @variables 
 * 				$args = receiving the post query
 * 				$r_string = recevied string in request for evaluation
 *  Version History:
 *  1.0 - Inititalizing the file to receive the request
 */
class test_model extends Model{
	function __construct(database $database) {
		parent::__construct();		

	}
	
	public function testfeed(){
	

	if ($_SERVER['REQUEST_METHOD'] == "POST"){	
		$string = file_get_contents('php://input');
		$args =$_POST;
		// for raw data testing from device
		$collection = $this->db->rawMaster;
		$options = array('fsync'=>\TRUE);

		

		// data insert			
		if (empty($args["query"])) {												// IF no string received
			$string = file_get_contents('php://input');
			   if(empty($string)){
						http_response_code(403);	
						$msg  =		"No Content"; 
						echo json_encode($msg);
						$collection->insert(array('msg'=>$args["query"]));
						exit;
					}else
				{
					$args["query"] = $string;
					$collection->insert(array('msg'=>$args["query"]));
				}
			}

		

			$r_string = explode(",", $args["query"]);
			//print_r($r_string);
			$device = 	substr($r_string[0], 2);									
			/*$collection = $this->db->DeviceMaster;
			$query = array("_id" => $device);
			// checking device
				if (empty($collection->findOne($query))) {	
					http_response_code(403);				
					$msg = "DEVICE NOT FOUND";
					echo json_encode($msg);
					exit;
				}
				else{*/
			/*		for($i=1;$i<=3;$i++){
						if (empty($r_string[$i])) {
							http_response_code(403);				
							$msg = $this->param[$i]." NOT FOUND";
							echo json_encode($msg);													
							exit;
						}
					}*/
					// device header information data
					$dt = DateTime::createFromFormat('d-m-YH:i:s', $r_string[1]);
					//print_r($r_string[1]);
					
					$data =array(
					"did"		=> $device,		
					// convert unix timestamp to iso and then to string for mongo date object					
					"dt"		=> new MongoDate($dt->getTimestamp()),
					"lat" 		=>$r_string[2],
					"long" 		=>$r_string[3],					
					);
					$index = -1;
					$data['data']=[];
					for ($i =4;$i<count($r_string)-1;$i++ )
					{ 
						// identifying the key
						// wether sensor id or sensor data
						$identifier = substr($r_string[$i], 0,1);								
						if($identifier == '#'){
							// getting sensor id
							$index++;
							$key =  'sensorID';
							$value =substr($r_string[$i], 1,strlen($r_string[$i]));
							array_push($data['data'] , array($key => $value));
							// array for sensor data
							$data['data'][$index]['sdata']=[];
						}else
						{
						// checking if sensor is created or not 
						// if sensor is not found on right place in parameter string index still remains -1
						if($index == -1){
							http_response_code(403);				
							$msg = "Parameters not valid";
							echo json_encode($msg);													
							exit;
						}
						$dataid = substr($r_string[$i],0,3);
						$datavalue = substr($r_string[$i],3,strlen($r_string[$i]));												
						if(substr($datavalue,strlen($datavalue)-1,strlen($datavalue)) == '*'){
							// removing * if present at the end of string
							$datavalue = substr($datavalue,0,strlen($datavalue)-1);							
						}
						$type = substr($r_string[$i],0,1);	
						// getting the reading type based on the first letter
						if($type =='T'){
							$type = 'Temp';
						}elseif($type =='L'){
							$type = 'Level';
						}else{
							$type='Other';
						}	
							// pushing in the reading array of the sensor based on $index
							array_push($data['data'][$index]['sdata'], array(
								'id'=>$dataid,
								'value'=>(double)$datavalue,
								'type'=>$type
								));
							
						}			// Breaking string for value
					}	
					
					$collection = $this->db->deviceData;
					$options = array('fsync'=>\TRUE);
					$result = $collection->insert($data,$options);
					http_response_code(200);				
					$msg = "Created";
					echo json_encode($msg);	
			//	}// end of device checking IF
			
	}
	else
	{
		$msg = "400 BAD REQUEST";
		echo $msg;	
	}
	}// end of get status*/
}// end of class
?>