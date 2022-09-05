(function ($) {
	console.log('jQuery init');

	$.ajax({
		url: '/wp-admin/admin-ajax.php',
		data: {
			'action': 'ajax_object_request',
		},
		success: (data) => {
			console.log(data)
		}
	})

})(jQuery);
