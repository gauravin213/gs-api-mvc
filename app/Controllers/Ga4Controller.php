<?php

namespace App\Controllers;
use App\Database as DB;
use App\Request;
use App\Respnse;
use App\Models\Ga4;
use App\Models\Postmeta;
class Ga4Controller 
{
	function __construct(){}

	public function purchase(){
	    
		$body = Request::all(); Respnse::json($body); exit;

		//cc
        $measurement_id = getenv('GOOGLE_ANALYTIC_GA4_ID');
        $api_secret     = getenv('GOOGLE_ANALYTIC_GA4_API_SECRET_KEY');
        $end_point      = 'https://www.google-analytics.com';
        $end_debug_url  = $end_point.'/debug/mp/collect?measurement_id='.$measurement_id.'&api_secret='.$api_secret;
        $end_url        = $end_point.'/mp/collect?measurement_id='.$measurement_id.'&api_secret='.$api_secret;
        $tz = 'America/Los_Angeles';
       	
        
        $timestamp = time();
        $dt = new \DateTime("now", new \DateTimeZone($tz)); //first argument "must" be a string
        $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
        $currentDate = $dt->format('F j, Y, g:i:s a');  
        
        $debug_arr = array();
        
        $timestamp_micros = floor(microtime(true) * 1000);
        
        $body = Request::all();   
        
        $user_info = "IP_ADDR: ".$_SERVER['REMOTE_ADDR'].', TIME: '.$currentDate;

        Postmeta::add_post_meta( $body['params']['transaction_id'], '_ga4_started', $user_info);
        
        $debug_arr['request'] = $body; 

        $uniqueClientId = ((float)mt_rand()/(float)getrandmax()) * 0x7FFFFFFF.".".time();
        
        $http = new \GuzzleHttp\Client();  
        
        $_ga_pushed = Postmeta::get_post_meta( $body['params']['transaction_id'] , '_ga4_success', true);
        if (!empty($_ga_pushed)) {
            Respnse::json(['ga4 tracked on server side']); 
            exit;
        } 
        
        $response = $http->post($end_debug_url, [
            'json' => [
                'client_id' => ($body['client_id'] && trim($body['client_id'])!='') ? trim($body['client_id']) : $uniqueClientId,
                 //"timestamp_micros"=> "{$timestamp_micros}",
                'non_personalized_ads' => true,
                'events' => [
                        [
                            'name' => $body['event'],
                            'params' => $body['params']
                        ]
                    ]
                ]
        ]);
        
        $resposeBody = $response->getBody();
        
        $resposeBody = json_decode($resposeBody, true);
         
       
        
        $debug_arr['validated'] = $resposeBody;  
        
        if(empty($resposeBody['validationMessages'])){   
            $response = $http->post($end_url, [
            'json' => [
                'client_id' => ($body['client_id'] && trim($body['client_id'])!='') ? trim($body['client_id']) : $uniqueClientId,
               // "timestamp_micros"=> "{$timestamp_micros}",
                "non_personalized_ads"=> true,
                'events' => [
                        [
                            'name' => $body['event'],
                            'params' => $body['params']
                        ]
                    ]
                ]
            ]);
            
            $resposeBody1 = $response->getBody();
            
            $resposeBody1 = json_decode($resposeBody1, true);
            
            $debug_arr['success'] = $resposeBody1;  
            
            Postmeta::add_post_meta( $body['params']['transaction_id'], '_ga4_success', '1');

            //@mail("lores.quickfix@gmail.com","GA4_DEBUG_S",json_encode($debug_arr));
            //@mail("paul.quickfix@gmail.com","GA4_DEBUG_S",json_encode($debug_arr));
            //@mail("adam.quickfix1@gmail.com","GA4_DEBUG_S",json_encode($debug_arr));
            //@mail("talyor.quickfix@gmail.com","GA4_DEBUG_S",json_encode($debug_arr));
            
            header('Content-Type: application/json; charset=utf-8'); 
        
            Respnse::json(["response" => $resposeBody, "response1" => $resposeBody1]); 
            exit;
            
        }else{
            
            Postmeta::add_post_meta( $body['params']['transaction_id'], '_ga4_success', '0');
            
            //@mail("lores.quickfix@gmail.com","GA4_DEBUG_F",json_encode($debug_arr));
            //@mail("paul.quickfix@gmail.com","GA4_DEBUG_F",json_encode($debug_arr));
            //@mail("adam.quickfix1@gmail.com","GA4_DEBUG_F",json_encode($debug_arr));
            //@mail("talyor.quickfix@gmail.com","GA4_DEBUG_F",json_encode($debug_arr));
            
            header('Content-Type: application/json; charset=utf-8');  

            Respnse::json(["response" => $resposeBody]); 
            exit;
        }
		//cc
		
		

	}
}