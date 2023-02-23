<?php
/*
* Google analytics server side order tracking 
* New Prosource - All Tracking IDs:
* GTM ID: GTM-MGCJ8XP 
* UA ID: UA-145858536-1
* GA4 ID: G-T9P6Y2FRMH
*/
add_action( 'woocommerce_thankyou', 'woocommerce_thankyou_UA_and_GA4_tracking', 10, 1 );
function woocommerce_thankyou_UA_and_GA4_tracking( $order_id ){

    if( ! $order_id ) return;

    $_ga_tracked_ua_custom = get_post_meta($order_id, '_ga_tracked_ua_custom'); //client side meta
    $_ga_tracked_ga4_custom = get_post_meta($order_id, '_ga_tracked_ga4_custom'); //client side meta
    $_ga_pushed = get_post_meta($order_id, '_ga_pushed'); //server side meta
    $_ga4_success = get_post_meta($order_id, '_ga4_success'); //server side meta
    
    custom_pre_load_scripts();

    //debug param
    $rrr = [];
    if (!empty($_ga_tracked_ua_custom) || !empty($_ga_tracked_ga4_custom) || !empty($_ga_pushed) || !empty($_ga4_success) ) {
        $rrr[0] = [
            '_ga_tracked_ua_custom' => $_ga_tracked_ua_custom,
            '_ga_tracked_ga4_custom' => $_ga_tracked_ga4_custom,
            '_ga_pushed' => $_ga_pushed,
            '_ga4_success' => $_ga4_success,
            'flag' => '1'
        ];
    }else{
        $rrr[1] = [
            '_ga_tracked_ua_custom' => $_ga_tracked_ua_custom,
            '_ga_tracked_ga4_custom' => $_ga_tracked_ga4_custom,
            '_ga_pushed' => $_ga_pushed,
            '_ga4_success' => $_ga4_success,
            'flag' => '2'
        ];
    }
    //update_post_meta($order_id, '_custom_track_debug', $rrr);
    //debug param

    if (!empty($_ga_tracked_ua_custom) || !empty($_ga_tracked_ga4_custom) || !empty($_ga_pushed) || !empty($_ga4_success) ) {
        return;
    }

    $purchase           = array();
    $add_shipping_info  = array();
    $add_payment_info   = array();
    $items              = array();
    $items_ga           = array();

    $order              = wc_get_order( $order_id );
    $transaction_id     = $order->get_transaction_id();     // Transaction ID. Required.
    $affiliation        = 'Prosourcediesel online store';   // Affiliation or store name.
    $total              = $order->get_total();              // Grand Total.
    $shipping           = $order->get_shipping_total();     // Shipping.
    $tax                = $order->get_total_tax();         // Tax.
    $currency           = 'USD';                            // local currency code.
    $coupon             = $order->get_coupon_codes();
    $shipping_method    = $order->get_shipping_method();
    $payment_method     = $order->get_payment_method();

    $purchase_ga = [
        'id'            => $order_id, 
        'affiliation'   => $affiliation,
        'revenue'       => $total, 
        'shipping'      => $shipping, 
        'tax'           => $tax
    ];

    $purchase_ga_server = [
      'ti'  =>  $order_id,      // transaction id
      'ta'  =>  $affiliation,   // affiliation
      'tr'  =>  $total,         // revenue
      'tt'  =>  $tax,           // tax
      'ts'  =>  $oshipping,     // shipping
      'pa'  =>  'purchase',     // prod actions
    ];

    $purchase_ga4 = array(
        'transaction_id' => (string) $order_id, //$transaction_id, 
        'affiliation'    => (string) $affiliation,
        'value'          => (float) $total, 
        'currency'       => (string) $currency,
        'tax'            => (float) $tax,
        'shipping'       => $shipping,
        'coupon'         => (string) (!empty($coupon))? reset($coupon) : '',
    );

    $purchase_ga4_server = array(
        'debug_mode' => true,
        'transaction_id' => (string) $order_id, //$transaction_id, 
        'affiliation'    => (string) $affiliation,
        'value'          => (float) $total, 
        'currency'       => (string) $currency,
        'tax'            => (float) $tax,
        'shipping'       => $shipping,
        'coupon'         => (string) (!empty($coupon))? reset($coupon) : '',
    );
    
    $i=1;
    foreach ( $order->get_items() as $item_id => $item ) {

        $product    = $item->get_product();
        $product_id = $item->get_product_id();
        $terms      = get_the_terms( $product_id, 'product_cat' ); 
        $cat_name = '';
        if (!empty($terms)) {
            foreach ( $terms as $term ) {
                $cat_name = $term->name; 
            }
        } 
       
        $name       = $item->get_name();    // Product name. Required.
        $sku        = $product->get_sku();  // SKU/code.
        $category   = $cat_name;            // Category or variation.
        $price      = round(($item->get_total()/$item->get_quantity()),2);
        $quantity   = $item->get_quantity(); // Quantity.

        $items_ga4[] = [
            'item_id'       =>  (string) $product_id,// Product ID (string). 
            'item_name'     =>  (string) $name, 
            'currency'      =>  (string) $currency,
            'item_brand'    =>  (string) 'prosourcediesel',
            'item_category' =>  (string) $category, 
            'price'         =>  (float) $price, 
            'quantity'      =>  (int) $quantity
        ];

        $items_ga[] = [
            'id'        =>  (string) $product_id,  // Product ID (string).
            //'sku'       =>  (string) $sku, 
            'name'      =>  (string) $name, 
            'category'  =>  (string) $category, 
            'price'     =>  (string) $price, 
            'quantity'  =>  (string) $quantity
        ];

        $purchase_ga_server['pr'.$i.'id']  =   $product_id;
        $purchase_ga_server['pr'.$i.'nm']  =   $name;
        $purchase_ga_server['pr'.$i.'pr']  =   $price;
        $purchase_ga_server['pr'.$i.'qt']  =   $quantity;
        $i++;

    }

    $purchase_ga['items']  = $items_ga;
    $purchase_ga4['items']  = $items_ga4;
    $purchase_ga4_server['items']  = $items_ga4;

    $purchase_ga4 = [
      'event'     => 'purchase',
      'ecommerce' => $purchase_ga4
    ];

    $purchase_ga4_server = [
      'event'     => 'purchase',
      'client_id' => '',
      'params'    => $purchase_ga4_server
    ];

    $pretty_print = 0; // 0 or 1
     if ($pretty_print) {
        $purchase_ga4        = json_encode($purchase_ga4, JSON_PRETTY_PRINT);
        $purchase_ga4_server = json_encode($purchase_ga4_server, JSON_PRETTY_PRINT);
        $purchase_ga         = json_encode($purchase_ga, JSON_PRETTY_PRINT);
        $purchase_ga_server  = json_encode($purchase_ga_server, JSON_PRETTY_PRINT);
    }else{
        $purchase_ga4        = json_encode($purchase_ga4);
        $purchase_ga4_server = json_encode($purchase_ga4_server);
        $purchase_ga         = json_encode($purchase_ga);
        $purchase_ga_server  = json_encode($purchase_ga_server);
    }
    custom_UA_and_GA4_tracking($order_id, $purchase_ga4, $purchase_ga4_server, $purchase_ga, $purchase_ga_server);
    

    //debug param
    $rrr[2] = [
        'data' => [
            'purchase_ga4' => $purchase_ga4,
            'purchase_ga4_server' => $purchase_ga4_server,
            'purchase_ga' => $purchase_ga,
            'purchase_ga_server' => $purchase_ga_server
        ]
    ];
    update_post_meta($order_id, '_custom_track_debug', $rrr);
    //debug param
}


function custom_UA_and_GA4_tracking($order_id, $purchase_ga4, $purchase_ga4_server, $purchase_ga, $purchase_ga_server){ 
    ?>
    <h1>UA GA4 tracked</h1>
    <input type="hidden" name="basic_auth_token" id="basic_auth_token" value='<?php echo base64_encode('prosourcediesel_app:x+U;dA~]q&OX');?>'>
    <input type="hidden" name="api_base_url" id="api_base_url" value='<?php echo home_url();?>'>
    <input type="hidden" name="order_id" id="order_id" value='<?php echo $order_id;?>'>
    <input type="hidden" name="purchase_ga4" id="purchase_ga4" value='<?php echo $purchase_ga4;?>'>
    <input type="hidden" name="purchase_ga4_server" id="purchase_ga4_server" value='<?php echo $purchase_ga4_server;?>'>
    <input type="hidden" name="purchase_ga" id="purchase_ga" value='<?php echo $purchase_ga;?>'>
    <input type="hidden" name="purchase_ga_server" id="purchase_ga_server" value='<?php echo $purchase_ga_server;?>'>
    <script type="text/javascript">
        const basic_auth_token    =  document.getElementById("basic_auth_token").value;
        const api_base_url        =  document.getElementById("api_base_url").value;
        const order_id            =  JSON.parse(document.getElementById("order_id").value);
        const purchase_ga4        =  JSON.parse(document.getElementById("purchase_ga4").value);
        const purchase_ga4_server =  JSON.parse(document.getElementById("purchase_ga4_server").value);
        const purchase_ga         =  JSON.parse(document.getElementById("purchase_ga").value);
        const purchase_ga_server  =  JSON.parse(document.getElementById("purchase_ga_server").value);
        const client_id           = getCookie('_ga');
        console.log('-------------------Custom UA and GA4 params start-------------------------');
        console.log('api_base_url: ', api_base_url);
        console.log('order_id: ', order_id);
        console.log('purchase_ga4: ', purchase_ga4);
        console.log('purchase_ga4_server: ', purchase_ga4_server);
        console.log('purchase_ga: ', purchase_ga);
        console.log('purchase_ga_server: ', purchase_ga_server);
        console.log('client_id: ', client_id);
        console.log('basic_auth_token: ', basic_auth_token);
        console.log('-------------------Custom UA and GA4 params end-------------------------');


        //GA4 fire event on client side
        function ga4_client_side_event() {
            console.log('ga4_client_slide_event_fired');
            dataLayer.push({ ecommerce: null }); 
            dataLayer.push(purchase_ga4);
        }

        //UA fire event on client side
        function ga_client_side_event() {
            console.log('ga_client_slide_event_fired');
            const purchase = {
                id: purchase_ga.id,
                affiliation: purchase_ga.affiliation,
                revenue: purchase_ga.revenue,
                shipping: purchase_ga.shipping,
                tax: purchase_ga.tax,
            };
            const items = purchase_ga.items;
            ga('create', 'UA-257189233-1'); // ==> 
            ga('require', 'ec');  
            if (items.length > 0) {
                items.forEach(item => {
                    console.log('item ga: ', item);
                    ga('ec:addProduct', item);  // ==> 
                });
            }
            ga('ec:setAction', 'purchase', purchase); // ==> 
            ga('send', 'pageview');
        }

        //GA4 fire event on server side
        function ga4_server_side_event() {
            console.log('ga4_server_side_event_fired');
            fetch(`${api_base_url}/gs-api-mvc/api/ga4/purchase`, { ///wp-ga4-api/purchase.php
              method: 'POST',
              headers: {
                "Content-Type": "application/json",
                'Authorization': `Basic ${basic_auth_token}`
              },
              body: JSON.stringify(purchase_ga4_server),
              redirect: 'follow'
            })
              .then(response => response.json())
              .then(result => {
                console.log('wp-ga4-api-response-ga4-purchase: ', result);
              })
              .catch(error => {
                console.log('error', error)
              });
        }

        //UA fire event on server side
        function ga_server_side_event() {
            console.log('ga_server_side_event_fired');
            fetch(`${api_base_url}/gs-api-mvc/api/ga/purchase`, { ///wp-ga4-api/purchasega.php
              method: 'POST',
              headers: {
                "Content-Type": "application/json",
                'Authorization': `Basic ${basic_auth_token}`
              },
              body: JSON.stringify(purchase_ga_server),
              redirect: 'follow'
            })
              .then(response => response.json())
              .then(result => {
                console.log('wp-ga-api-response-ga-purchase: ', result);
              })
              .catch(error => {
                console.log('error', error)
              });
        }


        function gs_update_post_meta(data) {
            console.log('gs_update_post_meta: ', data);
            
            fetch(`${api_base_url}/gs-api-mvc/api/postmeta/add`, { ///wp-ga4-api/postmeta.php
              method: 'POST',
              headers: {
                "Content-Type": "application/json",
                'Authorization': `Basic ${basic_auth_token}`
              },
              body: JSON.stringify(data),
              redirect: 'follow'
            })
              .then(response => response.json())
              .then(result => {
                console.log('wp-ga-api-response-postmeta: ', result);
              })
              .catch(error => {
                console.log('error', error)
              });
        }


        // check if ad blocker
        detectAdblock((isAdblockerDetected) => {
            if (isAdblockerDetected) {

                //Case 1: fire purchase event on server side
                console.log(`Case 1: ads are blocked`);

                ga4_server_side_event();
                ga_server_side_event();
                gs_update_post_meta({
                    "post_id": order_id,
                    "meta_key": "_tracked_by",
                    "meta_value": {
                        "order_id": order_id,
                        "tracked_by": "server", //server/client
                        "case": "1", 
                    }
                });
                              
            }else{

                if(client_id==''){

                    //Sace 2: fire purchase event on server side
                    console.log(`Case 2: ads are not blocked, client_id empty`);

                    ga4_server_side_event();
                    ga_server_side_event();
                    gs_update_post_meta({
                        "post_id": order_id,
                        "meta_key": "_tracked_by",
                        "meta_value": {
                            "order_id": order_id,
                            "tracked_by": "server", //server/client
                            "case": "2", 
                        }
                    });
                     
                }else{

                    //Case 3: fire purchase event on client side
                    console.log(`Case 3: ads are not blocked, client_id not empty`);

                    ga4_client_side_event();
                    ga_client_side_event();
                    gs_update_post_meta({
                        "post_id": order_id,
                        "meta_key": "_ga_tracked_ua_custom",
                        "meta_value": "1"
                    });
                    gs_update_post_meta({
                        "post_id": order_id,
                        "meta_key": "_ga_tracked_ga4_custom",
                        "meta_value": "1"
                    });
                    gs_update_post_meta({
                        "post_id": order_id,
                        "meta_key": "_tracked_by",
                        "meta_value": {
                            "order_id": order_id,
                            "tracked_by": "client", //server/client
                            "case": "3", 
                        }
                    });


                }
            }
        });

    </script>
    <?php

    //update_post_meta($order_id, '_ga_tracked_ua_custom', 1);
    //update_post_meta($order_id, '_ga_tracked_ga4_custom', 1);
}


function custom_pre_load_scripts(){ 
    ?>

    <script type="text/javascript">
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)
        [0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    </script>

    <script type="text/javascript">
        function setCookie(cname,cvalue,exdays) {
          const d = new Date();
          d.setTime(d.getTime() + (exdays*24*60*60*1000));
          let expires = "expires=" + d.toUTCString();
          document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
          let name = cname + "=";
          let decodedCookie = decodeURIComponent(document.cookie);
          let ca = decodedCookie.split(';');
          for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
              c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
              return c.substring(name.length, c.length);
            }
          }
          return "";
        }

        function checkCookie() {
          let user = getCookie("username");
          if (user != "") {
            alert("Welcome again " + user);
          } else {
             user = prompt("Please enter your name:","");
             if (user != "" && user != null) {
               setCookie("username", user, 30);
             }
          }
        }

        function detectAdblock(callback) {
            fetch('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', {
                method: 'HEAD',
                mode: 'no-cors',
            }).then((response) => {
                // If the request is redirected, then the ads are blocked.
                callback(response.redirected);
            }).catch(() => {
                // If the request fails completely, then the ads are blocked.
                callback(true);
            })
        }
    </script>
    <?php
}