<?php

$maps_int = WP_PLUGIN_DIR . '/maps-integration';

require_once $maps_int . '/maps-integration.php';

if ( ! wp_next_scheduled( 'fss_cron_hook' ) ) {
	wp_schedule_event( time(), 'one_minute', 'fss_cron_hook' );
}

function fss_cron_exec() {

	global $wpdb;

	$tablename = $wpdb->prefix . 'forminator_send_sms_data';

	$results = $wpdb->get_results(
		"SELECT * FROM $tablename"
	);

	$location = new Manage_Location();

	foreach ( $results as $row ) {

		$short_url = create_short_url( $row->location );

		$url = ! is_wp_error( $short_url ) ? $short_url : $row->location;

		ray( $location->use_maps_distance( $row->location ) )->red();

		ray( $url )->purple();

		$wpdb->update( $tablename, array( 'msg_status' => 3 ), array( 'name' => 'Elizabeth' ) );

		print_customer_name( $row->name );

	}

	// ray( $this->request_data )->orange();

	// $result = $this->send_data( json_encode( $messages ), 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', $this->username, $this->password );
}

function print_customer_name( $customer_name ) {
	ray( $customer_name )->green();
	echo $customer_name;
}

function create_short_url( $long_url ) {
	$redirect_res = wp_remote_get(
		'https://nchitotodayapp1.local/wp-json/nchito-maps/v1/maps',
		array(
			'body' => array(
				'timeout'   => 30,
				'target'    => $long_url,
				'sslverify' => false,
			),
		)
	);
	ray( $redirect_res )->orange();
	// Try to create a redirect.
	if ( 200 === $redirect_res['response']['code'] ) {
		$redirect_res['body'] = json_decode( $redirect_res['body'], true );
		$new_redirect         = $redirect_res['body']['body']['items'][0];
		$map_url              = 'https://nchito.day' . $new_redirect['url'];
		ray( $map_url )->green();
	} else {
		$map_url = $long_url;
	}
	return $map_url;
}

function send_data( $post_body, $url, $username, $password ) {
	$ch      = curl_init();
	$headers = array(
		'Content-Type:application/json',
		'Authorization:Basic ' . base64_encode( "$username:$password" ),
	);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_body );
	// Allow cUrl functions 20 seconds to execute.
	curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
	// Wait 10 seconds while trying to connect.
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	$output                    = array();
	$output['server_response'] = curl_exec( $ch );
	$curl_info                 = curl_getinfo( $ch );
	$output['http_status']     = $curl_info['http_code'];
	$output['error']           = curl_error( $ch );
	curl_close( $ch );
	return $output;
}


// echo 'Process completed...' . PHP_EOL;

// ray( $sms_job->custom_data )->orange();
