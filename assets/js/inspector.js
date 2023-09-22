/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { useEntityProp } from '@wordpress/core-data';
import { CheckboxControl, PanelRow } from '@wordpress/components';

/**
 * Internal dependencies
 */

function SettingsPanelPlugin() {
	const [meta, setMeta] = useEntityProp('postType', 'mailster-form', 'meta');

	const { hcaptcha } = meta;

	const title = hcaptcha
		? __('Captcha enabled', 'mailster')
		: __('Captcha disabled', 'mailster');

	return (
		<PluginDocumentSettingPanel name="hcaptcha" title={title}>
			<PanelRow>
				<CheckboxControl
					label={__('Enable hCaptcha', 'mailster')}
					help={__('Enable hCaptcha for this form.', 'mailster')}
					checked={!!hcaptcha}
					onChange={() => setMeta({ hcaptcha: !hcaptcha })}
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin('mailster-hcaptcha-settings-panel', {
	render: SettingsPanelPlugin,
	icon: false,
});
