	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php printf( __( 'You have to %s to get your public and private keys', 'mailster-hcaptcha' ), '<a href="https://dashboard.hcaptcha.com/welcome" class="external">' . __( 'sign up', 'mailster-hcaptcha' ) . '</a>' ); ?></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Site Key', 'mailster-hcaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[hCaptcha_public]" value="<?php echo esc_attr( mailster_option( 'hCaptcha_public' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Secret Key', 'mailster-hcaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[hCaptcha_private]" value="<?php echo esc_attr( mailster_option( 'hCaptcha_private' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Error Message', 'mailster-hcaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[hCaptcha_error_msg]" value="<?php echo esc_attr( mailster_option( 'hCaptcha_error_msg' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Disable for logged in users', 'mailster-hcaptcha' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[hCaptcha_loggedin]" value=""><input type="checkbox" name="mailster_options[hCaptcha_loggedin]" value="1" <?php checked( mailster_option( 'hCaptcha_loggedin' ) ); ?>> <?php esc_html_e( 'disable the hCaptcha™ for logged in users', 'mailster-hcaptcha' ); ?></label></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Forms', 'mailster-hcaptcha' ); ?><p class="description"><?php esc_html_e( 'select forms which require a captcha', 'mailster-hcaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php
				$forms       = mailster( 'forms' )->get_all();
					$enabled = mailster_option( 'hCaptcha_forms', array() );
				foreach ( $forms as $form ) {
					$form = (object) $form;
					$id   = isset( $form->ID ) ? $form->ID : $form->id;
					echo '<li><label><input name="mailster_options[hCaptcha_forms][]" type="checkbox" value="' . $id . '" ' . ( checked( in_array( $id, $enabled ), true, false ) ) . '>' . esc_html( $form->name ) . '</label></li>';
				}

				?>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Language', 'mailster-hcaptcha' ); ?></th>
			<td><select name="mailster_options[hCaptcha_language]">
				<?php
				$languages = array(
					'0'      => esc_html__( 'detect automatically', 'mailsterr-hcaptcha' ),
					'ar'     => 'Arabic',
					'bg'     => 'Bulgarian',
					'ca'     => 'Catalan',
					'zh-CN'  => 'Chinese (Simplified)',
					'zh-TW'  => 'Chinese (Traditional)',
					'hr'     => 'Croatian',
					'cs'     => 'Czech',
					'da'     => 'Danish',
					'nl'     => 'Dutch',
					'en-GB'  => 'English (UK)',
					'en'     => 'English (US)',
					'fil'    => 'Filipino',
					'fi'     => 'Finnish',
					'fr'     => 'French',
					'fr-CA'  => 'French (Canadian)',
					'de'     => 'German',
					'de-AT'  => 'German (Austria)',
					'de-CH'  => 'German (Switzerland)',
					'el'     => 'Greek',
					'iw'     => 'Hebrew',
					'hi'     => 'Hindi',
					'hu'     => 'Hungarain',
					'id'     => 'Indonesian',
					'it'     => 'Italian',
					'ja'     => 'Japanese',
					'ko'     => 'Korean',
					'lv'     => 'Latvian',
					'lt'     => 'Lithuanian',
					'no'     => 'Norwegian',
					'fa'     => 'Persian',
					'pl'     => 'Polish',
					'pt'     => 'Portuguese',
					'pt-BR'  => 'Portuguese (Brazil)',
					'pt-PT'  => 'Portuguese (Portugal)',
					'ro'     => 'Romanian',
					'ru'     => 'Russian',
					'sr'     => 'Serbian',
					'sk'     => 'Slovak',
					'sl'     => 'Slovenian',
					'es'     => 'Spanish',
					'es-419' => 'Spanish (Latin America)',
					'sv'     => 'Swedish',
					'th'     => 'Thai',
					'tr'     => 'Turkish',
					'uk'     => 'Uk rainian',
					'vi'     => 'Vietnamese',
				);

				$current = mailster_option( 'hCaptcha_language' );
				foreach ( $languages as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}

				?>
			</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Theme', 'mailster-hcaptcha' ); ?></th>
			<td><select name="mailster_options[hCaptcha_theme]">
				<?php
				$themes      = array(
					'light' => esc_html__( 'Light', 'mailster-hcaptcha' ),
					'dark'  => esc_html__( 'Dark', 'mailster-hcaptcha' ),
				);
					$current = mailster_option( 'hCaptcha_theme' );
				foreach ( $themes as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}
				?>
			</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Size', 'mailster-hcaptcha' ); ?></th>
			<td><select name="mailster_options[hCaptcha_size]">
				<?php
				$sizes   = array(
					'normal'  => esc_html__( 'Normal', 'mailster-hcaptcha' ),
					'compact' => esc_html__( 'Compact', 'mailster-hcaptcha' ),
				);
				$current = mailster_option( 'hCaptcha_size' );
				foreach ( $sizes as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}
				?>
			</select></td>
		</tr>
	</table>
