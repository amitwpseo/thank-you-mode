/* Thank You Mode - admin UI */
jQuery(function ($) {
	'use strict';

	/* ---- tabs ---- */
	function showTab(id) {
		if (!$('.tym-panel[data-panel="' + id + '"]').length) { id = 'design'; }
		$('.tym-tabs a').removeClass('is-active').filter('[data-tab="' + id + '"]').addClass('is-active');
		$('.tym-panel').removeClass('is-active').filter('[data-panel="' + id + '"]').addClass('is-active');
	}
	$('.tym-tabs a').on('click', function (e) {
		e.preventDefault();
		var id = $(this).data('tab');
		showTab(id);
		if (history.replaceState) { history.replaceState(null, '', '#' + id); }
	});
	showTab((location.hash || '').replace('#', '') || 'design');

	/* ---- colour pickers ---- */
	$('.tym-color').wpColorPicker();

	/* ---- media library pickers (logo + background) ---- */
	var frames = {};
	$('.tym-media-pick').on('click', function (e) {
		e.preventDefault();
		var $btn = $(this), target = $btn.data('target'), prev = $btn.data('prev');
		if (!frames[target]) {
			frames[target] = wp.media({
				title: $btn.data('title') || 'Choose image',
				button: { text: 'Use this image' },
				library: { type: 'image' },
				multiple: false
			});
			frames[target].on('select', function () {
				var a = frames[target].state().get('selection').first().toJSON();
				$(target).val(a.url).trigger('change');
				$(prev).attr('src', a.url).show();
			});
		}
		frames[target].open();
	});
	$('.tym-media-clear').on('click', function (e) {
		e.preventDefault();
		$($(this).data('target')).val('');
		$($(this).data('prev')).hide();
	});
	$('#tym-logo, #tym-bg').on('input change', function () {
		var $i = $(this), $p = $($i.attr('id') === 'tym-logo' ? '#tym-logo-prev' : '#tym-bg-prev');
		if ($i.val()) { $p.attr('src', $i.val()).show(); } else { $p.hide(); }
	});

	/* ---- design cards + live preview ---- */
	var $frame = $('#tym-frame'), $open = $('#tym-open'), currentDesign = $('.tym-opt input:checked').val() || 1;

	function previewSrc(bust) {
		var u = TYM.previewUrl + '&tym_design=' + currentDesign;
		return bust ? u + '&_=' + Date.now() : u;
	}
	function reloadPreview() {
		$frame.attr('src', previewSrc(true));
		$open.attr('href', previewSrc(false));
	}
	$('.tym-opt input').on('change', function () {
		$('.tym-opt').removeClass('is-sel');
		$(this).closest('.tym-opt').addClass('is-sel');
		currentDesign = $(this).val();
		reloadPreview();
	});
	$('#tym-refresh').on('click', reloadPreview);

	$('.tym-pv-tools [data-w]').on('click', function () {
		$('.tym-pv-tools [data-w]').removeClass('is-on');
		$(this).addClass('is-on');
		$frame.css('width', $(this).data('w'));
	});
});
