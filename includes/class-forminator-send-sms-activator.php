<?php

/**
 * Fired during plugin activation
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */
class Forminator_Send_Sms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		create_db();
	}

}

function create_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . 'forminator_send_sms_data';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name text(25) NOT NULL,
		phone varchar(15) NOT NULL,
		location varchar(2083) NOT NULL,
		request text(25) NOT NULL,
		message text(500) NOT NULL,
		msg_status bool NOT NULL,
		msg_sent_at datetime NOT NULL,
		form_id smallint(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
