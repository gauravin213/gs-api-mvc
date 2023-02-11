<?php

namespace App\Controllers;

//use GuzzleHttp\Client;

class PurchaseController 
{
	
	function __construct(){}

	public function purchase(){
		//echo json_encode(['string PurchaseController']);
		$http = new \GuzzleHttp\Client();
        $response = $http->get('https://nextdev.prosourcediesel.com/wp-lumen/api/v1/menu-items?menu_term_id=59324');
        $resposeBody = $response->getBody();
        $resposeBody = json_decode($resposeBody, true);
        echo json_encode($resposeBody);
	}
}

