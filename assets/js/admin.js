(function ($) {
	'use strict';

	// Select2 on site URL dropdown
	if ( $.fn.select2 ) {
		$('#cw_wfs_url').select2({
			placeholder: '— select site —',
			allowClear:  true,
			width:       '400px',
		});
	}

	var frame;

	$('#cw_wfs_screenshot_select').on('click', function (e) {
		e.preventDefault();

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title:    'Select Screenshot',
			button:   { text: 'Use this image' },
			multiple: false,
			library:  { type: 'image' },
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$('#cw_wfs_screenshot').val(attachment.id);
			var preview = $('#cw_wfs_screenshot_preview');
			preview.html('<img src="' + attachment.url + '" style="max-width:320px;height:auto;display:block;">');
			$('#cw_wfs_screenshot_remove').show();
		});

		frame.open();
	});

	$(document).on('click', '#cw_wfs_screenshot_remove', function (e) {
		e.preventDefault();
		$('#cw_wfs_screenshot').val('');
		$('#cw_wfs_screenshot_preview').empty();
		$(this).hide();
	});

})(jQuery);
