jQuery(document).ready(function($) {
	function dpp_render() {
		$('.dpp_render_area').each(function(index) {
			var this_id = $(this).attr('id');
			var shortcode = this_id.search('shortcode');

			if(shortcode > 0) {
				var shortcode_atts = $('#' + this_id + '_settings').html();
			}
			else {
				var widget_id = this_id;
				shortcode = '';
			}

			$.post(dpp_ajax.ajaxurl,{action: 'dpp_render', widget_id: widget_id, shortcode: shortcode, shortcode_atts: shortcode_atts}, function(data) {
				$('#'+this_id).html(data);
			});
		});
	}
	if($('.dpp_render_area').length) {
		dpp_render();
	}
});