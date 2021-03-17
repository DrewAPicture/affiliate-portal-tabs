<?php
/**
 * Core: Assets Loader
 *
 * @package     Affiliate Portal Tabs
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Drew A Picture Media, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
namespace WerdsWords\Core;

/**
 * Core class that handles loading assets needed by the plugin.
 *
 * @since 1.0.0
 */
class Assets_Loader {

	/**
	 * Sets up the loader hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );
		}
	}

	/**
	 * Registers and enqueues admin scripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		// Use minified libraries if SCRIPT_DEBUG is set to false.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Register scripts.
		wp_register_style( 'ww-apt-admin',          WW_APT_PLUGIN_URL . 'assets/css/admin' . $suffix . '.css',        array( 'dashicons' ), WW_APT_VERSION        );
		wp_register_script( 'ww-apt-admin-scripts', WW_APT_PLUGIN_URL . 'assets/js/admin-scripts' . $suffix . '.js',  array(),              WW_APT_VERSION, false );

		if ( affwp_is_admin_page( 'settings' ) && ( isset( $_REQUEST['tab'] ) && 'affiliate_portal_tabs' === $_REQUEST['tab'] ) ) {
			wp_enqueue_style( 'ww-apt-admin' );
			wp_enqueue_script( 'ww-apt-admin-scripts' );
		}
	}

}
