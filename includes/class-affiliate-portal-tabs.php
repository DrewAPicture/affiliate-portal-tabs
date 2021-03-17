<?php
/**
 * Affiliate Portal Tabs Plugin Bootstrap
 *
 * @package     Affiliate Portal Tabs
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Drew A Picture Media, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

use WerdsWords\Core;

if ( ! class_exists( 'WerdsWords_Affiliate_Portal_Tabs' ) ) {

	/**
	 * Main plugin bootstrap class for Affiliate Portal Tabs.
	 *
	 * @since 1.0.0
	 * @final
	 */
	final class WerdsWords_Affiliate_Portal_Tabs {

		/**
		 * The main static plugin instance.
		 *
		 * @since 1.0.0
		 * @var   WerdsWords_Affiliate_Portal_Tabs
		 * @static
		 */
		private static $instance;

		/**
		 * Version.
		 *
		 * @since 1.0.0
		 */
		private $version = '1.0.0';

		/**
		 * Main plugin file.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $file = '';

		/**
		 * Instance of the assets loader.
		 *
		 * @since 1.0.0
		 * @var   Core\Assets_Loader
		 */
		public $assets;

		/**
		 * Instance of the admin class.
		 *
		 * @since  1.0.0
		 * @var    \WerdsWords\Core\Admin
		 */
		public $admin;

		/**
		 * Retrieves an instance of the plugin.
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @since 1.0.0
		 * @static
		 *
		 * @param string $file Main plugin file.
		 * @return \WerdsWords_Affiliate_Portal_Tabs Bootstrap instance.
		 */
		public static function instance( $file = null ) {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WerdsWords_Affiliate_Portal_Tabs ) ) {

				self::$instance       = new \WerdsWords_Affiliate_Portal_Tabs;
				self::$instance->file = $file;

				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();
				self::$instance->setup_objects();
			}

			return self::$instance;
		}

		/**
		 * Throws an error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @access protected
		 * @since  1.0.0
		 *
		 * @return void
		 */
		protected function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'This object cannot be cloned.', 'affiliate-portal-tabs' ), '1.0.0' );
		}

		/**
		 * Disables unserializing of the class.
		 *
		 * @access protected
		 * @since  1.0.0
		 *
		 * @return void
		 */
		protected function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'This class cannot be unserialized.', 'affiliate-portal-tabs' ), '1.0.0' );
		}

		/**
		 * Sets up the class.
		 *
		 * @access private
		 * @since  1.0.0
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Resets the instance of the class.
		 *
		 * @access public
		 * @since  1.0.0
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0.0
		 *
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'WW_APT_VERSION' ) ) {
				define( 'WW_APT_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'WW_APT_PLUGIN_DIR' ) ) {
				define( 'WW_APT_PLUGIN_DIR', plugin_dir_path( $this->file ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WW_APT_PLUGIN_URL' ) ) {
				define( 'WW_APT_PLUGIN_URL', plugin_dir_url( $this->file ) );
			}

			// Plugin Root File
			if ( ! defined( 'WW_APT_PLUGIN_FILE' ) ) {
				define( 'WW_APT_PLUGIN_FILE', $this->file );
			}

		}

		/**
		 * Include necessary files.
		 *
		 * @access private
		 * @since  1.0.0
		 *
		 * @return void
		 */
		private function includes() {
			// Bring in the autoloader.
			require_once __DIR__ . '/lib/autoload.php';
		}

		/**
		 * Initializes the plugin.
		 *
		 * @since 1.0.0
		 */
		private function init() {}

		/**
		 * Setup all objects
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function setup_objects() {
			if ( is_admin() ) {
				self::$instance->admin = new Core\Admin;
			}

			self::$instance->assets = new Core\Assets_Loader;
		}

		/**
		 * Sets up the default hooks and actions.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function hooks() {
			// Render the tab content.
			add_filter( 'affwp_render_affiliate_dashboard_tab', array( $this, 'render_custom_tab' ), 10, 2 );

			// Redirect if non-affiliate tries to access a tab's page.
			add_action( 'template_redirect', array( $this, 'redirect' ) );

			// Hide tabs in the Affiliate Area.
			add_filter( 'affwp_affiliate_area_show_tab', array( $this, 'hide_existing_tabs' ), 10, 2 );

		}

		/**
		 * Filter an existing tab's content
		 *
		 * @since 1.1.2
		 * @param string $content
		 * @param string $active_tab The slug of the active tab.
		 *
		 * @return string $content The content to show in the tab.
		 */
		public function render_custom_tab( $content, $active_tab ) {

			if ( ! $this->functions->is_custom_tab( $active_tab ) ) {
				// Not a custom tab, return the content.
				return $content;
			}

			// Get the tab's content.
			$content = $this->custom_tab_content( $active_tab );

			// Return the $content.
			return $content;

		}

		/**
		 * The custom tab's content.
		 *
		 * @since 1.1.2
		 * @param string $active_tab The slug of the active tab.
		 *
		 * @return string $content The content of the tab
		 */
		public function custom_tab_content( $active_tab ) {
			?>
			<div id="affwp-affiliate-dashboard-tab-<?php echo $active_tab; ?>" class="affwp-tab-content">
				<?php echo $this->functions->get_custom_tab_content( $active_tab ); ?>
			</div>
			<?php
		}

		/**
		 * Hide tabs from the Affiliate Area.
		 *
		 * @since 1.1
		 *
		 * @return boolean
		 */
		public function hide_existing_tabs( $show, $tab ) {

			// Look in the new array for hidden tabs.
			$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

			if ( $tabs ) {
				foreach ( $tabs as $key => $tab_array ) {
					if ( isset( $tab_array['slug'] ) && $tab_array['slug'] === $tab && ( isset( $tab_array['hide'] ) && 'yes' === $tab_array['hide'] ) ) {
						$show = false;
					}
				}
			}

			return $show;

		}

		/**
		 * Affiliate Area Tabs.
		 *
		 * @since 1.2
		 *
		 * @return array $tabs The tabs to show in the Affiliate Area
		 */
		public function affiliate_area_tabs( $tabs ) {

			// Get the Affiliate Area Tabs.
			$affiliate_area_tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs' );

			if ( $affiliate_area_tabs ) {

				$new_tabs        = array();
				$saved_tab_slugs = array();

				// Create a new array in the needed format of tab slug => tab title.
				foreach ( $affiliate_area_tabs as $key => $tab ) {

					if ( isset( $tab['slug'] ) ) {
						$new_tabs[$tab['slug']] = $tab['title'];

						/**
						 * If the saved tab slug exists inside $affiliate_area_tabs, but not in $tabs (tabs from filter),
						 * and it's not a custom tab added via the admin then it should be unset from $affiliate_area_tabs immediately.
						 */
						if ( ! array_key_exists( $tab['slug'], $tabs ) && ! $this->functions->is_custom_tab( $tab['slug'] ) ) {

							/**
							 * Tabs added by add-ons should always be visible in the admin tab list
							 * and only visible in the Affiliate Area if the affiliate has access.
							 */
							if ( array_key_exists( $tab['slug'], $this->functions->add_on_tabs() ) && ! is_admin() ) {
								unset( $new_tabs[$tab['slug']] );
							} else {
								unset( $new_tabs[$tab['slug']] );
							}

						}

						// Store an array of tab slugs.
						$saved_tab_slugs[] = $tab['slug'];
					}

				}

				/**
				 * If the tab slug exists in $tabs (added via filter), but not in $affiliate_area_tabs (because its already been saved),
				 * and the tab slug isn't a custom tab (it's ID will be 0), append the tab to the end of $affiliate_area_tabs.
				 * That way it can be re-ordered by admin and saved into its new location.
				 */
				foreach ( $tabs as $tab_slug => $tab_title ) {

					if ( ! in_array( $tab_slug, $saved_tab_slugs ) && ! $this->functions->is_custom_tab( $tab_slug ) ) {
						$new_tabs[ $tab_slug ] = $tab_title;
					}
				}

				return $new_tabs;

			}

			return $tabs;

		}

		/**
		 * Redirect to affiliate login page if content is accessed.
		 *
		 * @since 1.0.1
		 * @return void
		 */
		public function redirect() {

			if ( ! affiliatewp_affiliate_area_tabs()->functions->protected_page_ids() ) {
				return;
			}

			$redirect = affiliate_wp()->settings->get( 'affiliates_page' ) ? get_permalink( affiliate_wp()->settings->get( 'affiliates_page' ) ) : site_url();
			$redirect = apply_filters( 'affiliatewp-affiliate-area-tabs', $redirect );

			if ( in_array( get_the_ID(), affiliatewp_affiliate_area_tabs()->functions->protected_page_ids() ) && ( ! affwp_is_affiliate() ) ) {
				wp_redirect( $redirect );
				exit;
			}

		}

	}

	/**
	 * Retrieves an instance of the plugin bootstrap.
	 *
	 * @since 1.0.0
	 *
	 * @return WerdsWords_Affiliate_Portal_Tabs Plugin instance.
	 */
	function ww_affiliate_portal_tabs() {
		return WerdsWords_Affiliate_Portal_Tabs::instance();
	}

}
