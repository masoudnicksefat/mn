jQuery(function($) {
	// fix container's height
	if ($("#content").length) {
		h = $(window).height() - $("#content").position().top + 10;
		$(".nr-main-container").css({
			"min-height" : h
		});
	}
})