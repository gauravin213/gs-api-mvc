<?php

namespace App\Controllers;
use App\Database as DB;
use App\Request;
use App\Respnse;
use App\Classes\GaEcommerceTracking;
use App\Models\Ga;
use App\Models\Postmeta;
class GaController 
{
	function __construct(){}

	public function purchase(){

		$body = Request::all(); Respnse::json($body); exit;

        //cc
        $body = Request::all();
        $conn = DB::conn();
        $order_id = $body['ti'];
        $_ga_pushed = Postmeta::get_post_meta($order_id, '_ga_pushed', true);
        if (!empty($_ga_pushed)) {
            Respnse::json(['ga tracked on server side']); 
            exit;
        }
        $GOOGLE_ANALYTIC_UA_ID   = getenv('GOOGLE_ANALYTIC_UA_ID');
        $GaEcommerceTracking = new GaEcommerceTracking( $GOOGLE_ANALYTIC_UA_ID, 'prosourcediesel Order #'.$order_id, false );
        $GaEcommerceTracking->send_hit( $body );
        Postmeta::update_post_meta($order_id, '_ga_pushed', '1');
        Respnse::json($body); 
        exit;
        //cc
	}
}