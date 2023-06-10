<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */
class Forminator_Send_Sms_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		self::unschedule_wp_cron_events();
	}

	public static function unschedule_wp_cron_events() {
		wp_clear_scheduled_hook( 'forminator_send_sms_cron_hook' );
	}

}
