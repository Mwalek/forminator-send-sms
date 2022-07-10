<?php

/**
 * The booking/request functionality of the plugin.
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/public
 */



/**
 * The booking/request functionality of the plugin.
 *
 * Defines the plugin name, version, and collects forminator form data
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/public
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */
class Forminator_Send_Sms_Booking {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
	 * The submitted data.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $request_data    The data submitted in the form.
	 */
	private $request_data;

	/**
	 * The BulkSMS username.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $username    The BulkSMS username for API connection.
	 */
	private $username;

    /**
	 * The BulkSMS password.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $password    The BulkSMS password for API connection.
	 */
	private $password;

	/**
	 * The Form IDs.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $form_ids    The form IDs that will connect to the API.
	 */
	private $form_ids;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {

		extract($config);
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->username = $username;
		$this->password = $password;
		$this->request_data = [];
		$this->form_ids = [2592, 100];
        // $this->show_data($config);

	}

    

    /**
	 * Save all the data entered into the forminator form.
	 *
	 * @since    1.0.0
	 */

	public function collect_form_data($response ) {

		$this->request_data = $_POST;
		error_log(print_r($this->request_data, true));
        $this->save_data($_POST);
		// Check if the submitted form in our list.
		if(in_array($_POST['form_id'], $this->form_ids)) {
			$this->prep_data($_POST);
			error_log('The form IDs match');
		} else {
			error_log('The form IDs do NOT match');
			return;

		}

	}

    private function save_data($data) {

        global $wpdb;
        $tablename = $wpdb->prefix.'custom_data';

        $wpdb->insert($tablename, array(
            'time' => date('Y-m-d H:i:s'),
            'name' => $data['name-1'],
            'phone' => $data['phone-1'],
            'location' => $data['url-1'],
            'message' => $data['textarea-1']
        ));

	}

	private function prep_data($data) {
		$name = $data['name-1'];
		$phone_number = $data['phone-1'];
		$location = $data['url-1'];

		$seller_phone_number = '260953138973';  // 260767924824
		$seller_message = "Hi! {$name} requested the Plumber service :). Location: {$location}";
		$buyer_message = "Hi {$name}, thanks for using NchitoToday! We've received your booking and will be in touch soon. You can also reach us on Whatsapp, 0767924824.";

		$messages = array(
			array('from'=> 'NchitoToday', 'to'=> $seller_phone_number, 'body'=> $seller_message),
			array('from'=> 'NchitoToday', 'to'=> $phone_number, 'body'=> $buyer_message)
		);

		$result = $this->send_data( json_encode($messages), 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', $this->username, $this->password );

	}

	private function send_data($post_body, $url, $username, $password) {
		$ch = curl_init( );
		$headers = array(
		'Content-Type:application/json',
		'Authorization:Basic '. base64_encode("$username:$password")
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
		// Allow cUrl functions 20 seconds to execute
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
		// Wait 10 seconds while trying to connect
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$output = array();
		$output['server_response'] = curl_exec( $ch );
		$curl_info = curl_getinfo( $ch );
		$output['http_status'] = $curl_info[ 'http_code' ];
		$output['error'] = curl_error($ch);
		curl_close( $ch );
		// if ($output['http_status'] == 201){
		// 	error_log("Success! Message(s) sent. Status code: {$output['http_status']}.");
		// } else {
		// 	error_log("There was a problem sending your message(s). Status code: {$output['http_status']}.");
		// }
		return $output;
	}

    public function show_data($data) {


	}

}
