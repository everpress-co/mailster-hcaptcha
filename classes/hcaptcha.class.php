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
		add_action( 'init', array( &$this, 'register_post_meta' ) );
	}

	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'hCaptcha_public'    => '',
				'hCaptcha_private'   => '',
				'hCaptcha_error_msg' => esc_html__( 'Captcha failed! Please reload the page and try again.', 'mailster-hcaptcha' ),
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

	public function deactivate( $network_wide ) {}

	public function init() {

		if ( is_admin() ) {

			add_filter( 'mailster_setting_sections', array( &$this, 'settings_tab' ) );
			add_action( 'mailster_section_tab_hCaptcha', array( &$this, 'settings' ) );

			add_action( 'enqueue_block_assets', array( &$this, 'enqueue_block_editor_assets' ) );
		}

		add_filter( 'mailster_form_fields', array( &$this, 'form_fields' ), 10, 3 );
		add_filter( 'mailster_block_form', array( &$this, 'add_form_fields' ), 10, 3 );

		add_filter( 'mailster_block_form', array( &$this, 'enqueue_script' ), 10, 3 );
		add_filter( 'mailster_form', array( &$this, 'enqueue_script' ), 10, 2 );
		add_filter( 'mailster_block_form_field_errors', array( &$this, 'form_submission_check' ), 10, 3 );
		add_filter( 'mailster_submit', array( &$this, 'legacy_form_submission_check' ), 10, 3 );

	}

	public function register_post_meta() {

		register_post_meta(
			'mailster-form',
			'hcaptcha',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,

			)
		);
	}


	public function enqueue_script( $output, $form_id, $args = null ) {

		// for block forms
		if ( is_array( $args ) ) {
			$enabled = get_post_meta( $form_id, 'hcaptcha', true );
			if ( ! $enabled ) {
				return $output;
			}
			$forms = array( $form_id );
		} else {
			$forms = mailster_option( 'hCaptcha_forms', array() );

			if ( ! in_array( $form_id, $forms ) ) {
				return $output;
			}
		}

		wp_enqueue_script( 'mailster_hcaptcha_script', $this->plugin_url . 'build/hcaptcha.js', array(), MAILSTER_VERSION );

		wp_localize_script(
			'mailster_hcaptcha_script',
			'mailster_hcaptcha',
			array(
				'public_key' => mailster_option( 'hCaptcha_public' ),
				'lang'       => mailster_option( 'hCaptcha_language' ),
				'forms'      => $forms,
			)
		);
		return $output;
	}

	public function enqueue_block_editor_assets() {

		// only on block forms
		if ( get_post_type() !== 'mailster-form' ) {
			return;
		}

		wp_enqueue_script( 'mailster_hcaptcha_inspector_script', $this->plugin_url . 'build/inspector.js', array(), MAILSTER_VERSION );
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

	public function add_form_fields( $output, $form_id, $args ) {

		if ( is_user_logged_in() && mailster_option( 'hCaptcha_loggedin' ) && ! is_admin() ) {
			return $output;
		}

		$enabled = get_post_meta( $form_id, 'hcaptcha', true );

		if ( ! $enabled ) {
			return $output;
		}

		return str_replace( '<div class="wp-block-mailster-field-submit', $this->get_field() . '<div class="wp-block-mailster-field-submit', $output );

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
				array( '_hcaptcha' => $this->get_field() ) +
				array_slice( $fields, $position, null, true );

		return $fields;

	}

	private function get_field() {

		return '<div class="mailster-wrapper mailster-_hcaptcha-wrapper"><div class="h-captcha" data-sitekey="' . esc_attr( mailster_option( 'hCaptcha_public' ) ) . '" data-theme="' . mailster_option( 'hCaptcha_theme', 'light' ) . '" data-size="' . mailster_option( 'hCaptcha_size', 'normal' ) . '"></div></div>';

	}

	public function check_captcha( $response ) {

		if ( isset( $response ) ) {

			$body = array(
				'secret'   => mailster_option( 'hCaptcha_private' ),
				'response' => $response,
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

		return true;

	}


	public function form_submission_check( $fields_errors, $entry, $request ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $fields_errors;
		}

		if ( ! empty( $fields_errors ) ) {
			return $fields_errors;
		}

		$url_params = $request->get_url_params();

		$form_id = (int) $url_params['id'];
		// legacy
		if ( ! $form_id ) {
			$form_id = $request->get_param( '_formid' );
		}

		$enabled = get_post_meta( $form_id, 'hcaptcha', true );

		if ( ! $enabled ) {
			return $fields_errors;
		}

		$response = $request->get_param( 'h-captcha-response' );

		$result = $this->check_captcha( $response );

		if ( is_wp_error( $result ) ) {
			$fields_errors[ $result->get_error_code() ] = $result->get_error_message();
		}

		return $fields_errors;
	}

	public function legacy_form_submission_check( $object ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $object;
		}

		$formid = isset( $_POST['formid'] ) ? intval( $_POST['formid'] ) : 1;

		if ( ! in_array( $formid, mailster_option( 'hCaptcha_forms', array() ) ) ) {
			return $object;
		}

		if ( isset( $_POST['h-captcha-response'] ) ) {

			$result = $this->check_captcha( $_POST['h-captcha-response'] );

			if ( is_wp_error( $result ) ) {
				$object['errors'][ $result->get_error_code() ] = $result->get_error_message();
			}
		}

		return $object;

	}
}
