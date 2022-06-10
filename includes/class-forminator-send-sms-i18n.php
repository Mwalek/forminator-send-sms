<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */
class Forminator_Send_Sms_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'forminator-send-sms',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
