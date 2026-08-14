(function (wp) {
	const {registerPlugin} = wp.plugins;
	const {Button} = wp.components;
	const {createElement: el, useState} = wp.element;
	const {PluginPostStatusInfo} = wp.editor;

	function PushButton() {
		const [busy, setBusy] = useState(false);
		const {createSuccessNotice, createErrorNotice} = wp.data.useDispatch('core/notices');

		const push = async () => {
			setBusy(true);

			try {
				const postId = wp.data.select('core/editor').getCurrentPostId();
				const response = await fetch('/yard/live-content/update?id=' + postId, {
					method: 'POST',
					headers: {'X-WP-Nonce': window.yardLiveContent.nonce},
				});

				if (!response.ok) {
					throw new Error(response.status);
				}

				createSuccessNotice('Push bericht verstuurd!', {type: 'snackbar'});
			} catch (error) {
				createErrorNotice('Het sturen van een push bericht is mislukt.', {type: 'snackbar'});
			} finally {
				setBusy(false);
			}
		};

		return el(
			PluginPostStatusInfo,
			null,
			el(Button, {variant: 'secondary', isBusy: busy, disabled: busy, onClick: push}, 'Stuur push bericht')
		);
	}

	registerPlugin('yard-live-content', {render: PushButton});
})(window.wp);
