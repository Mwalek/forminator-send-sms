<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mwale.me
 * @since      1.0.0
 *
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Forminator_Send_Sms
 * @subpackage Forminator_Send_Sms/includes
 * @author     Mwale Kalenga <mwale.kalenga@prosites.space>
 */
class Forminator_Send_Sms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Forminator_Send_Sms_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The BulkSMS username.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $version    The BulkSMS username for API connection.
	 */
	private $username;

    /**
	 * The BulkSMS password.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $version    The BulkSMS password for API connection.
	 */
	private $password;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct($config) {
		if ( defined( 'FORMINATOR_SEND_SMS_VERSION' ) ) {
			$this->version = FORMINATOR_SEND_SMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'forminator-send-sms';
		extract($config);
		$this->username = $username;
		$this->password = $password;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_booking_hooks($config);

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Forminator_Send_Sms_Loader. Orchestrates the hooks of the plugin.
	 * - Forminator_Send_Sms_i18n. Defines internationalization functionality.
	 * - Forminator_Send_Sms_Admin. Defines all hooks for the admin area.
	 * - Forminator_Send_Sms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-forminator-send-sms-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-forminator-send-sms-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-forminator-send-sms-admin.php';

		/**
		 * The class responsible for defining the majority of actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-forminator-send-sms-public.php';

		/**
		 * The class responsible for defining all actions related to bookings/requests
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-forminator-send-sms-booking.php';

		$this->loader = new Forminator_Send_Sms_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Forminator_Send_Sms_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Forminator_Send_Sms_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Forminator_Send_Sms_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Forminator_Send_Sms_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	private function define_booking_hooks($config) {

		$plugin_booking = new Forminator_Send_Sms_Booking( $this->get_plugin_name(), $this->get_version(), $config );

		$this->loader->add_action( 'forminator_form_after_handle_submit', $plugin_booking, 'collect_form_data');
		$this->loader->add_action( 'forminator_form_after_save_entry', $plugin_booking, 'collect_form_data');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Forminator_Send_Sms_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
