<?php
namespace App\Classes;
class SubscriptionStatus{
    var $server;

    public function __construct($server){
        $this->server = $server; // Assign server url
    }

    public function getStatus($address, $password, $applicationId){

        $arrayField = array("applicationId" => $applicationId,
            "password" => $password,
            "subscriberId" => $address
            );

        $jsonObjectFields = json_encode($arrayField);
        return $this->sendRequest($jsonObjectFields);
    }
	

    private function sendRequest($jsonObjectFields){
        $ch = curl_init($this->server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonObjectFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $this->handleResponse($res);
    }

    private function handleResponse($resp){
		
        if ($resp == "") {
            throw new SubscriptionStatusException
            ("Server URL is invalid", '500');
        } else {
            //echo $resp;
			return $resp;
        }
    }

}

class SubscriptionStatusException extends Exception{

    var $code;
    var $response;
    var $statusMessage;

    public function __construct($message, $code, $response = null){
        parent::__construct($message);
        $this->statusMessage = $message;
        $this->code = $code;
        $this->response = $response;
    }

    public function getStatusCode(){
        return $this->code;
    }

    public function getStatusMessage(){
        return $this->statusMessage;
    }

    public function getRawResponse(){
        return $this->response;
    }

}
?>