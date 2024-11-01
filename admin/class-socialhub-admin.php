<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://socialhub.io/
 * @since      1.0.0
 */

/**
 * Require JWT library.
 */
use \Firebase\JWT\JWT;

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 */
class SocialHub_Admin {

		const BASE_SLUG = 'socialhub-admin';

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct( $plugin_name, $version ) {
				$this->plugin_name = $plugin_name;
				$this->version = $version;
		}

		/**
		 * Register admin page.
		 *
		 * @since    1.0.0
		 */
		public static function admin_menu() {
				$hook = add_options_page(
						// Page title.
						__('SocialHub Integration', 'socialhub'),
						// Menu title.
						_x('SocialHub', 'menu title', 'socialhub'),
						// Capability.
						'edit_posts',
						// Menu slug.
						self::BASE_SLUG,
						// Callback.
						array(get_class(), 'render')
				);
		}

		/**
		 * Renders admin page.
		 *
		 * @since    1.0.0
		 */
		public static function render() {
				?>
				<div class="wrap">
						<h2><?php esc_html_e('SocialHub Integration', 'socialhub'); ?></h2>
						<br class="clear" />
						<strong><?php esc_html_e('This is the Access Token that will give the SocialHub access to your Blog in order to Integrate it.', 'socialhub'); ?></strong>
						<br class="clear" />
						<?php esc_html_e('Note that this Token will give SocialHub access to WordPress with the User you are currently logged in as. Meaning that all SocialHub will have the same capabilities as your user holds with his role. If you will reply to a comment from the SocialHub Inbox your reply will be created in the name of the user you were logged in with when you copied the Access Token. You might want to consider creating a new user to use for the SocialHub integration.', 'socialhub'); ?>
						<br class="clear" />
						<?php esc_html_e('To continue copy the Access Token and paste it into the WordPress Channel creation Interface at SocialHub: ', 'socialhub'); ?><a target="_blank" href="https://app.socialhub.io/#settings/channels">Add a WordPress Channel on SocialHub</a>
						<br class="clear" />
						<textarea readonly="true" rows="6" id="socialhub-jwt"><?php echo SocialHub_Admin::generate_token(wp_get_current_user()); ?></textarea>
				</div>
				<?php
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/socialhub-admin.css', array(), $this->version, 'all');
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/socialhub-admin.js', array(), $this->version, true);
		}

		/**
		 * Generate a JWT for given user.
		 *
		 * @since    1.0.0
		 * @param    [type] $user [description]
		 * @return   [type] [description]
		 */
		private static function generate_token($user) {
				$secret_key = AUTH_KEY;
				$custom_logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');

				$data = array(
						'iss' => SOCIALHUB_ISSUER,
						'iat' => time(),
						'plugin' => array(
								'version' => SOCIALHUB_VERSION,
						),
						'site' => array(
								'title' => get_bloginfo('name'),
								'customLogo' => ($custom_logo) ? $custom_logo[0] : null,
								'version' => get_bloginfo('version'),
								'home' => get_home_url(),
								'api' => get_rest_url(),
						),
						'user' => array(
								'id' => $user->data->ID,
								'name' => $user->data->user_login,
								'roles' => $user->roles,
						),
				);

				$token = JWT::encode($data, $secret_key);

				return $token;
		}

}
