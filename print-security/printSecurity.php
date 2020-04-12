<?php

/**
* The plugin bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
* @link              http://example.com
* @since             1.0.0
* @package           print-security
*
* @wordpress-plugin
* Plugin Name:       Print Security
* Description:       A plugin to check who connects to your website
* Version:           1.0.0
* Author:            Marco Maffei
* Author URI:        marcointhemiddle.com
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'WPINC' ) ) die;
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'printSecurity' ) ) {

	class printSecurity {

		protected $accessTable;
		protected $wpdb;
		protected $constant_name_prefix = 'PRNSEC_';

		public function __construct() {

			define( $this->constant_name_prefix .'ROOTDIR', plugin_dir_path(__FILE__) );
			register_activation_hook(__FILE__, array($this ,'printSecurityInstall') );
			register_deactivation_hook( __FILE__, array($this ,'printSecurityUninstall') );
			add_action( 'admin_menu', array( $this, 'printSecurityMenu' ) );
			add_action( 'wp_login', array( $this, 'last_login' ), 10, 2 );
			global $wpdb;
			$this->wpdb = $wpdb;
			$this->accessTable = $this->wpdb->prefix . "accessTable";

		}

		public static function get_user_ip_address() {

			if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ){
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$ip = apply_filters( 'user_ip_address', $ip );

			return $ip;

		}

		public function printSecurityInstall() {

			$sql = "CREATE TABLE $this->accessTable (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`user_login` varchar(255) NOT NULL,
			`user_id` int(11) NOT NULL,
			`user_ip`  varchar(255) NOT NULL,
			`user_log` int(11) NOT NULL,
			`user_rule` varchar(255) NOT NULL,
			PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);

		}

		public function printSecurityUninstall() {

			$users_id = get_users( array(
				'fields' => 'ID'
			) );

			foreach( $users_id as $user_id ){
				delete_user_meta( $user_id, 'printSecurity' );
				delete_user_meta( $user_id, 'printSecurity_count' );
				delete_user_meta( $user_id, 'user_ip_address' );
			}

			$sql = "DROP TABLE IF EXISTS $this->accessTable";
            $this->wpdb->query($sql);

		}

		public function printSecurityMenu() {

			add_menu_page('Print security List',
				'Print security Crud',
				'manage_options',
				'wpb_lastlogin',
				array($this, 'wpb_lastlogin')
			);

		}

		public function last_login( $user_login, $users ) {

			update_user_meta( $users->ID, 'printSecurity', time() );

			$printSecurity_count = get_user_meta( $users->ID, 'printSecurity_count', true );

			if( $printSecurity_count === false ){
				update_user_meta($users->ID, 'printSecurity_count', 1);
			} else {
				$printSecurity_new_value = intval($printSecurity_count);
				$printSecurity_new_value++;

				update_user_meta($users->ID, 'printSecurity_count', $printSecurity_new_value);
			}

			$ip = self::get_user_ip_address();
			
			update_user_meta( $users->ID, 'user_ip_address', $ip );

		}

		public function wpb_lastlogin() { 

			wp_register_style( 'custom_wp_admin_css', plugins_url('/css/bootstrap.min.css', __FILE__ ), false, '1.0.0' );
			wp_enqueue_style( 'custom_wp_admin_css' );
			add_action( 'admin_enqueue_scripts', 'wpStyle' );

			//$user = wp_get_current_user();
			$users = get_users();
			
			foreach ($users as $user_list) {

				$last_login = get_user_meta($user_list->ID, 'printSecurity');
				
				$user_how_many_times = get_user_meta($user_list->ID, 'printSecurity_count');

				$true_user_how_many_times = empty($user_how_many_times) ? 'not logged in yet' : $user_how_many_times[0];

				$last_login = empty($last_login) ? $the_login_date = 'Not logged in yet' : $the_login_date = date('M j, Y h:i:s a', $last_login[0]);

				$the_ip = get_user_meta($user_list->ID, 'user_ip_address');

				$true_ip = empty($the_ip) ? 'not ip yet' : $the_ip[0];

				$user_data[] = [
					'user_login'	=> $user_list->user_login,
					'user_id'		=> $user_list->ID,
					'user_ip'		=> $true_ip,
					'user_log'		=> $the_login_date,
					'user_rule'		=> $user_list->roles[0],
					'user_times'	=> $true_user_how_many_times
				];

			}
			
			require_once( PRNSEC_ROOTDIR . 'printSecurityMain.php' );

		}

	}

}

global $printSecurity;
$printSecurity = new printSecurity();
