<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Classes\Subscription;
use App\Classes\SubscriptionStatus;
use App\Classes\DirectDebitSender;
use App\Classes\SMSSender;
use App\Classes\UssdSender;
use App\Classes\UssdReceiver;
use Illuminate\Http\Request;

//use App\UssdSession;
//use App\Test;

class ListenerController extends Controller {


    public function smsListener(Request $request){
    
    
        $requestData = ['msisdn' =>$request->sourceAddress,
                 'msg_body'=>$request->message,
                 'app_id' =>$request->applicationId,
                 'requestId' =>$request->requestId
                ];

        $rules = [  'msisdn'=>'required',
                    'msg_body'=>'required',
                    'app_id'=>'required',
                    'requestId'=>'required'
                 ];
        
        $validator = Validator::make($requestData,$rules);
        if($validator->fails()){
            return "Invalid Request";
        }
        $applicationId   = $requestData['app_id'];
        $applicationPass = "36d1b5ac9f9d194450e89e8f9a2bca41";
        $sourceAddress   = $requestData['msisdn'];
        $requestId = $requestData['requestId'];
        $msg_body = $requestData['msg_body'];
        
       
    }

    public function ussdListener(Request $request){
        
        $sourceAddress  = $request->sourceAddress;
        $message        = $request->message;
        $requestId      = $request->requestId;
        $applicationId  = $request->applicationId;
        $encoding       = $request->encoding;
        $version        = $request->version;
        $sessionId      = $request->sessionId;
        $ussdOperation  = $request->ussdOperation;
        $applicationPass= \Config::get('constant.app_pass');
       
	//dd($applicationPass);
  //       $responseMsg = array("main" => "1. Activate 
        // 0. Exit");
        
        if ($ussdOperation  == "mo-init") { 
            try {
                $res = $this->checkSubscription($applicationId,$applicationPass,$sourceAddress);
               $this->sendUSSD($sessionId,$res,$sourceAddress,'mt-fin');
                $sub_stat = $this->subscribeUser($applicationId,$applicationPass,$sourceAddress);
  
            } catch (Exception $e) {
                $this->sendUSSD($sessionId, 'Sorry error occured try again',$sourceAddress );
            }
    
        }else{
            
        }
    }

    public function subscribeUser($appID,$appPass,$destinationAddress){   
	        
        $subscriptionServer = "https://developer.bdapps.com/subscription/send";
        $subscripSenderObj = new Subscription($subscriptionServer);
        $applicationId = $appID;
        $action = 1;
        $encoding = "0";
        $version =  "1.0";
        $password = $appPass;
        $binary_header = "";
        $res = $subscripSenderObj->subscribe($destinationAddress, $password, $applicationId, $action, $version, $binary_header); 
        $responseArray = json_decode($res,true);
        return $responseArray;
        
    }

    public function unSubscribeUser($appID,$appPass,$destinationAddress){   
  
        $subscriptionServer = "https://developer.bdapps.com/subscription/send";
        $subscripSenderObj = new Subscription($subscriptionServer);
        $applicationId = $appID;
        $action = 0;
        $encoding = "0";
        $version =  "1.0";
        $password = $appPass;
        $binary_header = "";
        $res = $subscripSenderObj->subscribe($destinationAddress, $password, $applicationId, $action, $version, $binary_header); 
        $responseArray = json_decode($res,true);
      return ($responseArray['subscriptionStatus'] == 'REGISTERED') ? true : false;
    }

    public function checkSubscription($appID,$appPass,$destinationAddress){
      $subscriptionServer = "https://developer.bdapps.com/subscription/getstatus";
      $subscripSenderObj = new Subscription($subscriptionServer);

      $password = $appPass;
      $applicationId = $appID;

      $res = $subscripSenderObj->subscriptionCheck($destinationAddress, $password, $applicationId); 
      $responseArray = json_decode($res,true);
      return $responseArray['subscriptionStatus'];

      
     }


    public function sendUSSD($sessionId, $responseMsg,$destinationAddress,$ussdOperation='mo-cont'){
        //dd($responseMsg);
        $applicationId = \Config::get('constant.app_id');
        $applicationPass = \Config::get('constant.app_pass');

        $ussdSender = new UssdSender('https://developer.bdapps.com/ussd/send',$applicationId,$applicationPass);
        
        $ussdSender->ussd($sessionId, $responseMsg,$destinationAddress,$ussdOperation );

    }

    public function test(){
        echo "Hello";
        //dd('route testing ok for women tips');
    }

}
