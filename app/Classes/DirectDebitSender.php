<?php
namespace App\Classes;

use App\Classes\Core;
use App\CaasHistory;
class DirectDebitSender extends core{

    var $server;

    var $applicationId;

    var $password;
    var $status_code;
    var $status_detail;
    var $ch_raw_request;
    var $ch_raw_response;
    var $msisdn;
			

    public function __construct($server,$applicationId,$password){

        $this->server = $server;

        $this->applicationId = $applicationId;

        $this->password = $password;

    }

    /*

        Get parameters form the application

        check one or more addresses

        Send them to cassMany

    **/

    public function cass( $externalTrxId, $subscriberId, $amount){

       $this->msisdn = $subscriberId;

        if (is_array($subscriberId)) {

            return $this->cassMany( $externalTrxId, $subscriberId,  $amount);

        } else if (is_string($subscriberId) && trim($subscriberId) != "") {

            return $this->cassMany( $externalTrxId, $subscriberId,  $amount);

        } else {

            throw new Exception("Address should be a string or a array of strings");

        }

    }


    private function cassMany($externalTrxId, $subscriberId, $amount){

        
        $arrayField = array(

				        	"applicationId" => $this->applicationId, 

				            "password" => $this->password,

				            "externalTrxId" => $externalTrxId,

				            "subscriberId" => $subscriberId,

				            "amount" => $amount

				        );

        $jsonObjectFields = json_encode($arrayField); 
        $this->ch_raw_request=$jsonObjectFields;

        return $this->handleResponse(json_decode($this->sendRequest($jsonObjectFields,$this->server)));

    }

    public function handleResponse($jsonResponse){



        $statusCode = $jsonResponse->statusCode;
        $statusDetail = $jsonResponse->statusDetail;
        $this->ch_raw_response=$jsonResponse;
        $this->status_code=$statusCode;
        $this->status_detail=$statusDetail;
        
        $caas = new CaasHistory;
        $caas->status_code = $statusCode;
        $caas->status_detail = $statusDetail;
        $caas->msisdn = $this->msisdn;
        $caas->save();
        if(empty($jsonResponse))

            throw new CassException('Invalid server URL', '500');

        if(strcmp($statusCode, 'S1000')==0)
            return true;
        else 
            return false;      
               
    }
     public function getRaw_request(){
        return $this->ch_raw_request;
    }
     public function getRaw_response(){
        return $this->ch_raw_response;
    }
    public function getstatusCode(){
        return $this->status_code;
    }
     public function getstatusDetail(){
        return $this->status_detail;
    }

    
}


?>
