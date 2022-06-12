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
	 * @var      array    $version    The data submitted in the form.
	 */
	private $request_data;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->request_data = [];
        //[$username, $password] = $config;
        $this->show_data($config);

	}

    

    /**
	 * Save all the data entered into the forminator form.
	 *
	 * @since    1.0.0
	 */

	public function collect_form_data($response ) {

		$this->request_data = $_POST;
        var_dump($this->request_data);

	}

    public function show_data($config) {

        var_dump($config);

	}

}
