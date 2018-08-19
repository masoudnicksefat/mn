jQuery(function($) {
	$(".pluginState").click(function() {
		var $el   = $(this),
            obj   = $el.closest(".nr-app-addons"),
			id    = $el.closest("tr").data("id"),
			span  = $el.find("span"),
			state = span.hasClass("icon-publish") ? 0 : 1;

        $.ajax({ 
            url: obj.data("base") + "index.php?option=com_gsd&view=items&format=raw&do=pluginState&plugin_id=" + id + "&state=" + state,
            success: function(response) {
            	span.removeAttr("class").addClass(response == "1" ? "icon-publish" : "icon-unpublish");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

        return false;
	})
})