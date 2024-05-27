<?php
/*
Plugin Name: Mailster hCaptcha
Requires Plugins: mailster
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=hCaptcha
Description: Adds a hCaptcha to your Mailster Subscription forms
Version: 2.0.1
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-hcaptcha
License: GPLv2 or later
*/
define( 'MAILSTER_HCAPTCHA_VERSION', '2.0.1' );
define( 'MAILSTER_HCAPTCHA_REQUIRED_VERSION', '3.3.3' );
define( 'MAILSTER_HCAPTCHA_FILE', __FILE__ );

require_once __DIR__ . '/classes/hcaptcha.class.php';
new MailsterHcaptcha();
