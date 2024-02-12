<?php
	add_action( 'wp_enqueue_scripts', 'wprms_theme_enqueue_styles' );
	function wprms_theme_enqueue_styles() {
	    //wp_register_style( 'wprms-bootstrap', WPRMS_PLUGIN_URL."/assets/css/bootstrap.css" );
	    wp_register_style( 'wprms-intlTelInput', WPRMS_PLUGIN_URL."/assets/css/intlTelInput.css" );
	    wp_register_style( 'wprms-style', WPRMS_PLUGIN_URL."/assets/css/reserve-my-spot.css?", array(), time() );

	    wp_register_script( 'wprms-intlTelInput-js', WPRMS_PLUGIN_URL."/assets/js/intlTelInput-jquery.min.js", array('jquery') );
	    wp_register_script( 'wprms-intl-tel-input-utils-js', WPRMS_PLUGIN_URL."/assets/js/intl-tel-input_11.0.4_js_utils.js", array('jquery') );
	    //wp_register_script( 'wprms-bootstrap', WPRMS_PLUGIN_URL."/assets/js/bootstrap.js", array('jquery'), time() );
	    wp_register_script( 'wprms-js', WPRMS_PLUGIN_URL."/assets/js/reserve-my-spot.js", array('jquery'), time() );
	    wp_register_script( 'wprms-lightbox-js', WPRMS_PLUGIN_URL."/assets/js/reserve-my-spot-lightbox.js", array('jquery'), time() );
	    wp_localize_script('wprms-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

	}
	
	function wprms_tag_a_subscriber($email = NULL, $tag_id = NULL){
		global $wprms_options;
		$url 		= "https://api.convertkit.com/v3/tags/$tag_id/subscribe";
		$api_secret = $wprms_options['api_secret'];
		$data = array(
					'api_secret'  	=> $api_secret,
					'email' 		=> $email,
				);
		$response = wp_remote_post( $url, array(
					    'body'    	=> $data,
					    'headers' 	=> array(
					        "Accept" =>  "application/json",
                  			"X-CKJS-Version" => "6"
					    ),
					) );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		wprms_generate_log($body);
	}

	function wprms_convert_to_est($userDate) {
		$userTimeZone = getTimeZoneFromIpAddress();
		wprms_generate_log('User Timezone');	
		wprms_generate_log($userTimeZone);
		// Create a DateTime object with the user's input and original time zone
	    $userDateTime = new DateTime($userDate, new DateTimeZone($userTimeZone));

	    // Set the time zone to Eastern Standard Time (EST)
	    $estTimeZone = new DateTimeZone('America/New_York');
	    $userDateTime->setTimezone($estTimeZone);

	    // Format the date and time in EST
	    $estTime = $userDateTime->format('Y-m-d H:i');

	    return $estTime;
	}


	function wprms_pass_to_convertkit_callback() {
		global $wprms_options;
	    $api_key = $wprms_options['api_key'];
	    $form_id = $wprms_options['form_id'];

	    if(empty($form_id))
	    	return 'form_id_not_found';

		$url 			= "https://app.convertkit.com/forms/$form_id/subscriptions";
	    $posted_data 	= $_POST['data'];
	    $date 	= $posted_data['date'];
	    $time 	= $posted_data['time'];
	    $aff 	= $posted_data['aff'];
	    $webinar_date = $webinar_time = $webinar_date_utc = "";

	    wprms_generate_log("Posted Data");
	    wprms_generate_log($_POST);

	    if($date == "0"){
	    	$webinar_date  = $webinar_time = 'watch directly';
	    }elseif($date == '1'){
	    	$time_selector_local_date = date('Y-m-d');
	    	$time_selector_local_time = $time;

	    	$webinar_date  = $webinar_date_utc = date('Y-m-d')." ".$time;
	    	$webinar_date = wprms_convert_to_est($webinar_date);
	    	$webinar_time = date('Y-m-d H:i', strtotime($webinar_date ."-3 minutes" ));
			
	    }else{
	    	$time_selector_local_date = date("Y-m-d", strtotime($date));
	    	$time_selector_local_time = $time;

    		$webinar_date = $webinar_date_utc = date("Y-m-d ".$time, strtotime($date));
    		$webinar_date = wprms_convert_to_est($webinar_date);
    		$webinar_time = date('Y-m-d H:i', strtotime($webinar_date ."-3 minutes" ));
	    }
	    // echo "success<br>";
	    // echo $webinar_date_utc."<br>";
	    // echo $webinar_date."<br>";
	    // exit();

	    $data = array(
				'api_key'  			=> $api_key,
				'first_name' 		=> $posted_data['last_name'],
				'email_address' 	=> $posted_data['email_address'],
				'ckjs_version' 		=> $_POST['ckjs_version'],
				'user' 				=> $_POST['user'],
				'fields' 			=> array(
											'webinar_time' => $webinar_time,
											'time_selector_local_date' => $time_selector_local_date,
											'time_selector_local_time' => $time_selector_local_time,
											'aff' => $aff,
										)
			);

	    wprms_generate_log("Data pass to convertkit");
	    wprms_generate_log($data);
	    $response = wp_remote_post( $url, array(
					    'body'    => $data,
					    'headers' => array(
					        "Accept" 			=>  "application/json",
                  			"X-CKJS-Version" 	=> "6"
					    ),
					) );

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		// echo "<pre>";
		// print_r($data);
		// print_r($posted_data);
		// print_r($body);
		// echo "</pre>";

		wprms_generate_log("Response of ConvertKit");
		wprms_generate_log($body);

		if( isset($body['status']) && $body['status'] == "success"){
			$tag_id = "";
			$timeselector_tag_id =  $wprms_options['timeselector_tag_id'];
			$redirect_url_main = $wprms_options['webinar_link_for_female'];
			$redirect_url = wprms_create_redirect_url($redirect_url_main);

			if($posted_data['gender'] == "man"){
	    		$tag_id =  $wprms_options['male_tag_id'];
				$redirect_url_main = $wprms_options['webinar_link_for_male'];
				$redirect_url = wprms_create_redirect_url($redirect_url_main);
			}else{
	    		$tag_id =  $wprms_options['female_tag_id'];
			}
			
			wprms_generate_log("Tag ID : ".$tag_id);
			/*
				Logic to add a tag to the subscriber using email_address
			*/
			wprms_tag_a_subscriber($data['email_address'], $tag_id);
			wprms_tag_a_subscriber($data['email_address'], $timeselector_tag_id);
			
			//$is_valid_date = wprms_validateDate($webinar_date_utc);
			//if( $is_valid_date ){
			if( !empty($webinar_date_utc) ){
				$timezone_from 	= getTimeZoneFromIpAddress();
		 		$newDateTime 	= new DateTime($webinar_date_utc, new DateTimeZone($timezone_from)); 
				$newDateTime->setTimezone(new DateTimeZone("UTC")); 
				$dateTimeUTC 	= $newDateTime->format("Y-m-d H:i");

				$body = "Your Masterclass “Invisible to Interesting” starts in 15 minutes. Click here to go to the webinar: ".$redirect_url;
				wprms_init_twilio($posted_data['full_phone'], $body, $dateTimeUTC);

				$body = "Friendly reminder that your SocialSelf registration for Invisible to Interesting ends shortly. Check email from “SocialSelf” ";
				wprms_init_twilio($posted_data['full_phone'], $body, $webinar_date_utc, 5);
			}

			/*
				Redirect URL logic on webpage
			*/
			if($webinar_date == "watch directly"){
		     	$user_redirect_url = $wprms_options['webinar_link_for_female'];
		     	if($posted_data['gender'] == "man"){
		     		$user_redirect_url = $wprms_options['webinar_link_for_male'];
		     	}
		    }else{
		    	/*
		    		Appending start and end date to the thank you page
		    	*/
		     	$user_redirect_url  = $wprms_options['thankyou_page_link'];
		     	$webinar_end_time   = date('y-m-d H:i:s', strtotime($webinar_date ."+60 minutes" ));
		     	$encryptor 			= new SimpleEncryptor();
				$encryptedString_a 	= $encryptor->encrypt($webinar_date);
				$encryptedString_b 	= $encryptor->encrypt($webinar_end_time);
				
				$query = parse_url($user_redirect_url, PHP_URL_QUERY);
				// Returns a string if the URL has parameters or NULL if not
				if ($query) {
				    $user_redirect_url .= "&a=".$encryptedString_a."&b=".$encryptedString_b;
				} else {
				    $user_redirect_url .= "?a=".$encryptedString_a."&b=".$encryptedString_b;
				}
		    }

			wp_send_json_success( array(
			     'redirect_url' => $user_redirect_url,
			), 200 );

			wp_die();
		}

	    wp_die();
	}
	add_action('wp_ajax_pass_to_convertkit', 'wprms_pass_to_convertkit_callback'); 
	add_action('wp_ajax_nopriv_pass_to_convertkit', 'wprms_pass_to_convertkit_callback'); 

	function wprms_validateDate($date, $format = 'Y-m-d H:i'){
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}

	function get_date_difference($datetime_1, $datetime_2){
		 
		$start_datetime = new DateTime($datetime_1); 
		$diff = $start_datetime->diff(new DateTime($datetime_2)); 
		$difference = array(
							'total_days' 	=> $diff->days,
							'years' 		=> $diff->y,
							'months' 		=> $diff->m,
							'days' 			=> $diff->d,
							'hours' 		=> $diff->h,
							'minutes' 		=> $diff->i,
							'seconds' 		=> $diff->s
						);
		return $difference; 
	}

	function wprms_init_twilio($to = NULL, $body = NULL, $date = NULL, $days = NULL){
		global $wprms_options;
		/*
			Client Account from Theme options
		*/
		$sid 		 		= $TWILIO_ACCOUNT_SID = $wprms_options['twilio_account_sid'];
		$token 		 		= $TWILIO_AUTH_TOKEN = $wprms_options['twilio_auth_token'];
		$from_phone  		= $wprms_options['twilio_from_phone'];
		$message_service_id = $wprms_options['twilio_message_ssid'];

		$date 			= date('y-m-d H:i:s', strtotime($date ."-15 minutes" ));
		$iso8601Date 	= date("c", strtotime($date));
		$iso8601Date 	= $iso8601Date.'Z';

		if($days > 0){
			$webinar_time_temp = new DateTime($date);
			$webinar_time_temp->modify('+5 days');
			$webinar_time_temp->setTime(17, 0, 0);
			$date = $webinar_time_temp->format('Y-m-d H:i:s');	
			$iso8601Date 	= date("c", strtotime($date));
			$iso8601Date 	= $iso8601Date.'Z';
		}

		// Your Account SID and Auth Token from console.twilio.com
		$client = new Twilio\Rest\Client($sid, $token);
		$log_details = array(
								'to' => $to,
								array(
									"body" => $body,
	                               	"from" => $from_phone,
	                               	"messagingServiceSid" => $message_service_id,
	                               	"sendAt" => new \DateTime($iso8601Date),
	                               	"scheduleType" => "fixed"
	                           	)
							);
		
		wprms_generate_log("Twilio Array");
		wprms_generate_log($log_details);
		
		try {
			$response = $client->messages->create(
							$to,
                           [
                               "body" => $body,
                               "from" => $from_phone,
                               "messagingServiceSid" => $message_service_id,
                               "sendAt" => new \DateTime($iso8601Date),
                               "scheduleType" => "fixed"
                           ]
                  );	
		} catch (Exception $e) {
		 	wprms_generate_log("Twilio Error Code :".$e->getStatusCode());
		 	wprms_generate_log("Twilio Error Details :".$e);
		}

		wprms_generate_log("Message ID :".$response->sid);

		return true;
	}
	
	function wprms_create_redirect_url($redirect_url){
		$query = parse_url($redirect_url, PHP_URL_QUERY);
		// Returns a string if the URL has parameters or NULL if not
		if ($query) {
		    $redirect_url .= "&token=".strtotime('now');
		} else {
		    $redirect_url .= "?token=".strtotime('now');
		}
		return $redirect_url;
	}

	function wprms_init_manage_redirect(){
		global $wprms_options;
		parse_str($_SERVER['QUERY_STRING'], $query_parameters);
		if( isset($query_parameters['token']) && !empty($query_parameters['token']) ){
			$date_1	= date('y-m-d H:i:s', strtotime('now'));
			$date_2	= date('y-m-d H:i:s', $query_parameters['token']);
			$result = get_date_difference($date_1, $date_2);
			if($result['hours'] >= 2 && $result['minutes'] > 1){
				wp_redirect( $wprms_options['too_late_page_link']);
				exit();
			}
		}
	}
	add_action('init', 'wprms_init_manage_redirect');


	function wprms_get_tag_lists(){
		global $wprms_options;
	    $api_key = $wprms_options['api_key'];
	    $form_id = $wprms_options['form_id'];
	    
	    if(isset($_GET['test'])){
	    	$body = "Friendly reminder that your SocialSelf registration for Invisible to Interesting ends shortly. Check email from “SocialSelf” ";
	    	echo $body;
	    	$date = '2023-11-28 05:00';
	    	$webinar_time = new DateTime($date);
			$webinar_time->modify('+5 days');
			$webinar_time->setTime(12, 0, 0);
			$deliveryDateFormatted = $webinar_time->format('Y-m-d H:i:s');
			echo $deliveryDateFormatted;
	    }

	    // if(isset($_GET['test'])){
		//     $userTimeZone = getTimeZoneFromIpAddress();
		//     echo $userTimeZone;
		//     $clientsIpAddress = wprms_client_ip_address();
		//     echo $clientsIpAddress;
		//     $clientInformation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientsIpAddress));
        // 	echo "<pre>";
        // 	print_r($clientInformation);
        // 	echo "</pre>";
		// }

	    // $webinar_date_utc = '2023-11-02 17:10';
	    // $timezone_from 	= getTimeZoneFromIpAddress();
 		// $newDateTime 	= new DateTime($webinar_date_utc, new DateTimeZone($timezone_from)); 
		// $newDateTime->setTimezone(new DateTimeZone("UTC")); 
		// $dateTimeUTC 	= $newDateTime->format("Y-m-d h:i");
		// $body = "IST time was ".$webinar_date_utc." <br> and UTC time was ".$dateTimeUTC;
		// echo $body;
		// wprms_init_twilio('+19178228150', $body, $dateTimeUTC);
		
		// $dateTime = '2023-11-1 10:48'; 
		// $timezone_from = getTimeZoneFromIpAddress();
 		// $newDateTime = new DateTime($dateTime, new DateTimeZone($timezone_from)); 
		// $newDateTime->setTimezone(new DateTimeZone("UTC")); 
		// $dateTimeUTC = $newDateTime->format("Y-m-d h:i");
		// echo $dateTimeUTC;
		// exit();	
		// $response = wp_remote_get( 'https://api.convertkit.com/v3/tags?api_key='.$api_key );
		// if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		// 	$headers = $response['headers']; // array of http header lines
		// 	$body    = $response['body']; // use the content
		// 	echo "<pre>";
		// 	print_r($body);
		// 	echo "</pre>";
		// }
	}
	add_action('init', 'wprms_get_tag_lists');
?>
