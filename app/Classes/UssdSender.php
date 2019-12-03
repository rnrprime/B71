<?php


namespace App\Classes;
use Exception;
use App\UssdSession;
class UssdSender extends Core{

    	private $applicationId,

			$password,

			$charging_amount='',

			$encoding='',

			$version='',

			$deliveryStatusRequest='',

			$binaryHeader='',

			$sourceAddress='',

			$serverURL,
            $raw_request,
            $raw_reponse,
            $status_code,
            $status_details;

    public function __construct($server,$applicationId,$password){

        $this->serverURL = $server; 

        $this->applicationId = $applicationId; 

        $this->password = $password; 

    }


    public function ussd( $sessionId, $message, $destinationAddress, $ussdOperation='mo-cont'){


        if (is_array($destinationAddress)) { 

            return $this->ussdMany($message,$sessionId, $ussdOperation, $destinationAddress);

				

        } else if (is_string($destinationAddress) && trim($destinationAddress) != "") {
			//dd($destinationAddress);
            return $this->ussdMany($message,$sessionId, $ussdOperation, $destinationAddress);

        } else {

            throw new Exception("address should a string or a array of strings");

        }

    }


    private function ussdMany($message,$sessionId, $ussdOperation, $destinationAddress)

	{


        $arrayField = array("applicationId" => $this->applicationId,

            "password" => $this->password,

            "message" => $message,

            "destinationAddress" => $destinationAddress,

            "sessionId" => $sessionId,

            "ussdOperation" => $ussdOperation,

            "encoding" => "440"

			);


        $jsonObjectFields = json_encode($arrayField);
        $this->raw_request=$jsonObjectFields;
		//dd($jsonObjectFields);
        $this->raw_reponse= $this->sendRequest($jsonObjectFields,$this->serverURL);
         //dd($this->raw_reponse);
        $response=json_decode($this->raw_reponse,true);
		//dd($response);
        $this->status_code=$response['statusCode'];
        $this->status_details=$response['statusDetail'];

        return $this->raw_reponse;
    }


    private function handleResponse($resp){
        
        if ($resp == "") {

            return "Error Occured";

        } else {

            echo $resp;
            
        }

    }

public function getApplicationId(){
    return $this->applicationId;
}
public function getPassword(){
    return $this->password;
}
public function getSessionId(){
    return $this->sessionId;
}
public function getMessage(){
    return $this->message;
}
public function getdestinationAddress(){
    return $this->destinationAddress;
}
public function getUssdOperation(){
    return $this->ussdOperation;
}
public function getRaw_request(){
    return $this->raw_request;
}
public function getRaw_response(){
    return $this->raw_reponse;
}
public function getStatus_code(){
    return $this->status_code;
}
public function getStatus_details(){
    return $this->status_details;
}


}
?>