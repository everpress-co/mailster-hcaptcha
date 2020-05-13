<?php

class MailsterHCaptcha {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_HCAPTCHA_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_HCAPTCHA_FILE );

		register_activation_hook( MAILSTER_HCAPTCHA_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_HCAPTCHA_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-hcaptcha' );

		add_action( 'init', array( &$this, 'init' ) );
	}

	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'hCaptcha_public'    => '',
				'hCaptcha_private'   => '',
				'hCaptcha_error_msg' => esc_html__( 'Please proof that you are human!', 'mailster-hcaptcha' ),
				'hCaptcha_loggedin'  => false,
				'hCaptcha_forms'     => array(),
				'hCaptcha_language'  => 0,
				'hCaptcha_theme'     => 'light',
				'hCaptcha_size'      => 'normal',
			);

			$mailster_options = mailster_options();

			foreach ( $defaults as $key => $value ) {
				if ( ! isset( $mailster_options[ $key ] ) ) {
					mailster_update_option( $key, $value );
				}
			}
		}

	}

	public function deactivate( $network_wide ) {

	}

	public function init() {

		if ( is_admin() ) {

			add_filter( 'mailster_setting_sections', array( &$this, 'settings_tab' ) );
			add_action( 'mailster_section_tab_hCaptcha', array( &$this, 'settings' ) );

		}

		add_filter( 'mailster_form_fields', array( &$this, 'form_fields' ), 10, 3 );
		add_filter( 'mailster_verify_subscriber', array( &$this, 'check_captcha' ), 10, 1 );
	}

	public function settings_tab( $settings ) {

		$position = 4;
		$settings = array_slice( $settings, 0, $position, true ) +
					array( 'hCaptcha' => 'hCaptcha' ) +
					array_slice( $settings, $position, null, true );

		return $settings;
	}

	public function settings() {
		include $this->plugin_path . '/views/settings.php';
	}

	public function form_fields( $fields, $formid, $form ) {

		if ( is_user_logged_in() && mailster_option( 'hCaptcha_loggedin' ) && ! is_admin() ) {
			return $fields;
		}

		if ( ! in_array( $formid, mailster_option( 'hCaptcha_forms', array() ) ) ) {
			return $fields;
		}

		$position = count( $fields ) - 1;
		$fields   = array_slice( $fields, 0, $position, true ) +
					array( '_hcaptcha' => $this->get_field( $form ) ) +
					array_slice( $fields, $position, null, true );

		return $fields;

	}

	public function get_field( $form ) {

		wp_enqueue_script( 'mailster_hcaptcha_script', 'https://www.hcaptcha.com/1/api.js?hl=' . mailster_option( 'hCaptcha_language' ), array(), '1.0', true );

		$identifieer = 'mailster-_hcaptcha-' . $form->ID . '-' . uniqid();

		$html = '<div class="mailster-wrapper mailster-_recaptcha-wrapper"><div class="h-captcha" data-sitekey="' . esc_attr( mailster_option( 'hCaptcha_public' ) ) . '" data-theme="' . mailster_option( 'hCaptcha_theme', 'light' ) . '" data-size="' . mailster_option( 'hCaptcha_size', 'normal' ) . '"></div></div>';

		wp_print_scripts( 'mailster_hcaptcha_script' );
		return $html;

	}

	public function check_captcha( $entry ) {

		if ( is_user_logged_in() && mailster_option( 'hCaptcha_loggedin' ) ) {
			return $entry;
		}

		$formid = isset( $_POST['formid'] ) ? intval( $_POST['formid'] ) : 1;

		if ( ! in_array( $formid, mailster_option( 'hCaptcha_forms', array() ) ) ) {
			return $entry;
		}

		if ( isset( $_POST['h-captcha-response'] ) ) {

			$body = array(
				'secret'   => mailster_option( 'hCaptcha_private' ),
				'response' => $_POST['h-captcha-response'],
			);

			$url = 'https://hcaptcha.com/siteverify';

			$response = wp_remote_post( $url, array( 'body' => $body ) );

			if ( is_wp_error( $response ) ) {
				return new WP_Error( '_hcaptcha', $response->get_error_message() );
			} else {
				$response = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! $response->success ) {
					return new WP_Error( '_hcaptcha', mailster_option( 'hCaptcha_error_msg' ) );
				}
			}
		} elseif ( ! is_admin() && get_query_var( '_mailster_page' ) != 'confirm' ) {
			return new WP_Error( '_hcaptcha', mailster_option( 'hCaptcha_error_msg' ) );
		}

		return $entry;

	}

}
