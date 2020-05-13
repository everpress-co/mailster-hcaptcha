<?php
/*
Plugin Name: Mailster hCaptcha
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=Mailster+hCaptcha&utm_medium=plugin
Description: Adds a hCaptcha to your Mailster Subscription forms
Version: 1.0
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-hcaptcha
License: GPLv2 or later
*/
define( 'MAILSTER_HCAPTCHA_VERSION', '1.0' );
define( 'MAILSTER_HCAPTCHA_REQUIRED_VERSION', '2.4' );
define( 'MAILSTER_HCAPTCHA_FILE', __FILE__ );

require_once dirname( __FILE__ ) . '/classes/hcaptcha.class.php';
new MailsterHcaptcha();
