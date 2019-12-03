<?php


/*******   Sender   ********/

namespace App\Classes;

class SMSReceiver{

	private $version;

	private	$applicationId;		

	private	$sourceAddress;	

	private $message;			

	private	$requestId;			

	private $encoding;			

	private $thejson;			

	

	public function __construct(){



		$jsonRequest = json_decode(file_get_contents('php://input'));

		file_put_contents("test.txt",print_r($jsonRequest,true));

        //$header=apache_request_headers();

        //file_put_contents("test.txt",print_r($jsonRequest,true));

		if(!(isset(

				$jsonRequest->version,	
				$jsonRequest->applicationId,
				$jsonRequest->address,
				$jsonRequest->message,
				$jsonRequest->requestId,
				$jsonRequest->encoding		
				))) 				

				if(!((isset($jsonRequest->sourceAddress) && isset($jsonRequest->message) )))

					$response = array('statusCode'=>'E1312', 'statusDetail'=>'Request is Invalid.');

		else{

			$this->thejson=$jsonRequest ;
			$this->version = $jsonRequest->version;
			$this->applicationId = $jsonRequest->applicationId;
			$this->sourceAddress = $jsonRequest->sourceAddress;
			$this->message = $jsonRequest->message;
			$this->requestId = $jsonRequest->requestId;
			$this->encoding = $jsonRequest->encoding;
				
			$response = array('statusCode'=>'S1000',

			 				  'statusDetail'=>'Process completed successfully.'
			 				);

		}
		

	  return response()->json($response,200)->send();


	}
	
	// Get the version of the incomming message

	public function sendJson(){
		return $this->thejson;
	}

	public function getVersion(){
		return $this->version;
	}
	
	// Get the encoding of the incomming message
	public function getEncoding(){
		return $this->encoding;
	}
	
	// Get the Application ID of the incomming message

	public function getApplicationId(){
		return $this->applicationId;

	}



	// Get the address of the incomming message

	public function getAddress(){

		$msisdn=explode(':', $this->sourceAddress);
		return end($msisdn);

	}



	// Get the Message of the incomming request	

	public function getMessage(){

		return $this->message;

	}



	// Get the unique requestId of the incomming message	

	public function getRequestId(){

		return $this->requestId;

	}



	// Get the json

	public function getJson(){

		return $this->thejson;

	}



}

?>