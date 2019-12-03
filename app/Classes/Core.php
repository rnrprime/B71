<?php
namespace App\Classes;
use App\UssdSession;
/********* This is the Core Class **********/
class Core

{

	public function sendRequest($jsonStream,$url){

		
		$ch = curl_init($url);
      //  curl_setopt($ch, CURLOPT_INTERFACE,'160.153.131.140');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStream);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);

        curl_close($ch);



		return $res;



	}

}




?>
