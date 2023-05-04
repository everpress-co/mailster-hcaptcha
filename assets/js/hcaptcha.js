(function () {
	'use strict';

	var script;
	var form_ids = mailster_hcaptcha ? mailster_hcaptcha.forms : [];
	var queue = [];

	// legacy forms
	form_ids.forEach(function (id) {
		var forms = document.querySelectorAll('.mailster-form-' + id);
		forms.forEach(init);
	});

	// block forms
	document.addEventListener('mailster:load', handler);
	document.addEventListener('mailster:open', handler);

	function handler(event) {
		if (!event.detail.el) return;
		var formEl = event.detail.el;
		init(formEl);
	}

	function init(formEl) {
		if (!script) {
			script = document.createElement('script');
			script.type = 'text/javascript';

			script.src =
				'https://www.hcaptcha.com/1/api.js?hl=' + mailster_hcaptcha.lang;

			document.getElementsByTagName('head')[0].appendChild(script);
		}
		queue.push(formEl);
	}
})();
