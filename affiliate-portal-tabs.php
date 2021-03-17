<?php
/**
 * Plugin Name: Affiliate Portal Tabs for AffiliateWP
 * Plugin URI: https://werdswords.com/plugins/affiliate-portal-tabs/
 * Description: Manage built-in tabs and external links in the Affiliate Portal
 * Author: Drew Jaynes
 * Author URI: https://werdswords.com/
 * Version: 1.0.0
 * Text Domain: affiliate-portal-tabs
 * Domain Path: languages
 *
 * This plugin is a fork of the Affiliate Area Tabs plugin Copyright (c) 2021 Sandhills Development, LLC
 *
 * Affiliate Portal Tabs is distributed under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 2 of the License,
 * or any later version.
 */

if ( ! class_exists( 'Affiliate_WP_Requirements_Check' ) ) {
	require_once dirname( __FILE__ ) . '/includes/lib/class-affiliate-wp-requirements-check.php';
}

/**
 * Class used to check requirements for and bootstrap the plugin.
 *
 * @since 1.0.0
 *
 * @see Affiliate_WP_Requirements_Check
 */
class WerdsWords_Affiliate_Portal_Tabs_Check extends Affiliate_WP_Requirements_Check {

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $slug = 'affiliate-portal-tabs';

	/**
	 * Add-on requirements.
	 *
	 * @since 1.0.0
	 * @var   array[]
	 */
	protected $addon_requirements = array(
		// AffiliateWP.
		'affwp' => array(
			'minimum' => '2.6.4.1',
			'name'    => 'AffiliateWP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// Affiliate Portal.
		'affwp_affiliate_portal' => array(
			'minimum' => '1.0.0-beta3.1',
			'name'    => 'AffiliateWP - Affiliate Portal',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// WordPress.
		'wp' => array(
			'minimum' => '5.0.0',
			'name'    => 'WordPress',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

	);

	/**
	 * Bootstrap everything.
	 *
	 * @since 1.0.0
	 */
	public function bootstrap() {
		if ( ! class_exists( 'Affiliate_WP' ) ) {

			if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
				require_once 'includes/lib/class-activation.php';
			}

			// AffiliateWP activation
			if ( ! class_exists( 'Affiliate_WP' ) ) {
				$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
				$activation = $activation->run();
			}
		} else {
			\WerdsWords_Affiliate_Portal_Tabs::instance( __FILE__ );
		}
	}

	/**
	 * Loads the add-on.
	 *
	 * @since 1.0.0
	 */
	protected function load() {
		// Maybe include the bundled bootstrapper.
		if ( ! class_exists( 'WerdsWords_Affiliate_Portal_Tabs' ) ) {
			require_once dirname( __FILE__ ) . '/includes/class-affiliate-portal-tabs.php';
		}

		// Maybe hook-in the bootstrapper.
		if ( class_exists( 'WerdsWords_Affiliate_Portal_Tabs' ) ) {

			// Bootstrap to plugins_loaded before priority 10 to make sure
			// add-ons are loaded after us.
			add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 100 );

			// Register the activation hook.
			register_activation_hook( __FILE__, array( $this, 'install' ) );
		}
	}

	/**
	 * Plugin-specific aria label text to describe the requirements link.
	 *
	 * @since 1.0.0
	 *
	 * @return string Aria label text.
	 */
	protected function unmet_requirements_label() {
		return esc_html__( 'Affiliate Portal Tabs Requirements', 'affiliatewp-affiliate-portal' );
	}

	/**
	 * Plugin-specific text used in CSS to identify attribute IDs and classes.
	 *
	 * @since 1.0.0
	 *
	 * @return string CSS selector.
	 */
	protected function unmet_requirements_name() {
		return 'affiliate-portal-tabs-requirements';
	}

	/**
	 * Plugin specific URL for an external requirements page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Unmet requirements URL.
	 */
	protected function unmet_requirements_url() {
		return 'https://werdswords.com/plugins/affiliate-portal-tabs/';
	}

	/**
	 * Checks the Affiliate Portal version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Affiliate Portal version.
	 */
	protected function check_affwp_affiliate_portal() {
		return get_option( 'affwp_ap_version' );
	}
}

$requirements = new WerdsWords_Affiliate_Portal_Tabs_Check( __FILE__ );

$requirements->maybe_load();
