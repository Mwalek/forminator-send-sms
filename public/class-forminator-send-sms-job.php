<?php

/**
 * The job/request functionality of the plugin.
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/public
 */

/**
 * The job/request functionality of the plugin.
 *
 * Defines the plugin name, version, and collects forminator form data
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/public
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */

if (!defined('ABSPATH')) {
	exit;
}

class Forminator_Send_Sms_Job {

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
	 * @var      string    $username    The BulkSMS username for API connection.
	 */
	private $username;

    /**
	 * The BulkSMS password.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $password    The BulkSMS password for API connection.
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

	public $custom_data;

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
		$this->username = "";
		$this->password = "";
		$this->request_data = [];
		$this->custom_data = [];
		$this->form_ids = [];
        // $this->show_data($config);

		$this->load_dependencies();

	}

    private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-forminator-send-sms-loader.php';

		$maps_integration_path = WP_PLUGIN_DIR . "/maps-integration/maps-integration.php";

		require_once $maps_integration_path;

		$this->loader = new Forminator_Send_Sms_Loader();

		$this->manage_locations = new Manage_Location();
	}

	public function collect_form_data($response) {

		$custom_data = apply_filters('forminator_send_sms_prepare_form_data', $_POST);

		$this->request_data = $_POST;

		$this->custom_data = $custom_data;

		$this->form_ids = $custom_data[3];

		$this->username = $custom_data[4]['username'];
		
		$this->password = $custom_data[4]['password'];

		// Prepare data for BulkSMS if the submitted form is on our list.
        if(in_array($_POST['form_id'], $this->form_ids)) {

			$this->organize_data($custom_data);		

			$this->show_data($response, $custom_data);

		}

	}

	private function organize_data($custom_data) {

		$count = [];
		$messages = [];

		function check_strings_in_array($arr) {
			return array_sum(array_map('is_string', $arr)) == count($arr);
		}

		foreach( $custom_data as $item ) {
			if (is_array($item)) {
				array_push($count, count($item));
			} else {
				array_push($count, null);
			}
		}

		// Don't proceed if number of recipients doesn't matach templates supplied
		if ( $count[1] == $count[2] ) {

			for( $i = 0; $i < $count[2]; $i++) {

				// Check if all  phonenumbers should be used or only the first one
				if (gettype($custom_data[0]) == "string") {
					$arr = array('from'=> $custom_data[0], 'to'=> $custom_data[1][$i], 'body'=> $custom_data[2][$i]);
				} else {
					$arr = array('from'=> $custom_data[0][$i], 'to'=> $custom_data[1][$i], 'body'=> $custom_data[2][$i]);
				}

				// Don't add $arr[] to $messages[] if it isn't made up entirely of strings.

				if (check_strings_in_array($arr)) {
					array_push($messages, $arr);
				} else {
					error_log("Error: Message inputs should comprise of strings only.");
				}

			}

			error_log(json_encode($messages));

			$this->prepare_data($messages);

			return $messages;



		} else {
			error_log("Error: The number of templates supplied (" . $count[2] . ") should match the number of recipients (" . $count[3] . ").");
		}

	} 

	private function prepare_data($messages) {

		$result = $this->send_data( json_encode($messages), 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', $this->username, $this->password );

		error_log(json_encode($result));

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
		if ($output['http_status'] == 201){
			error_log("Success! Message(s) sent. Status code: {$output['http_status']}.");
		} else {
			error_log("There was a problem sending your message(s). Status code: {$output['http_status']}.");
		}
		return $output;
	}

    public function show_data($data, $filter_data) {

		error_log(json_encode($filter_data));

	}

}
