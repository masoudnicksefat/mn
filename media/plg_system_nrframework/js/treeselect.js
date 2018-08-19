(function($) {
	$(document).ready(function() {
		$(".nr_treeselect").each(function() {
			var controls = $(this).find("div.nr_treeselect-controls");
			var list = $(this).find("ul.nr_treeselect-ul");
			var menu = $(this).find("div.nr_treeselect-menu-block").html();
			var maxheight = list.css("max-height");
			list.find("li").each(function() {
				var $li = $(this);
				var $div = $li.find("div.nr_treeselect-item:first");
				$li.prepend('<span class="pull-left icon-"></span>');
				$div.after('<div class="clearfix"></div>');
				if ($li.find("ul.nr_treeselect-sub").length) {
					$li.find("span.icon-").addClass("nr_treeselect-toggle icon-minus");
					$div.find("label:first").after(menu);
					if (!$li.find("ul.nr_treeselect-sub ul.nr_treeselect-sub").length) $li.find("div.nr_treeselect-menu-expand").remove();
				}
			});
			list.find("span.nr_treeselect-toggle").click(function() {
				var $icon = $(this);
				if ($icon.parent().find("ul.nr_treeselect-sub").is(":visible")) {
					$icon.removeClass("icon-minus").addClass("icon-plus");
					$icon.parent().find("ul.nr_treeselect-sub").hide();
					$icon.parent().find("ul.nr_treeselect-sub span.nr_treeselect-toggle").removeClass("icon-minus").addClass("icon-plus");
				} else {
					$icon.removeClass("icon-plus").addClass("icon-minus");
					$icon.parent().find("ul.nr_treeselect-sub").show();
					$icon.parent().find("ul.nr_treeselect-sub span.nr_treeselect-toggle").removeClass("icon-plus").addClass("icon-minus");
				}
			});
			controls.find("input.nr_treeselect-filter").keyup(function() {
				var $text = $(this).val().toLowerCase();
				list.find("li").each(function() {
					var $li = $(this);
					if ($li.text().toLowerCase().indexOf($text) == -1) $li.hide();
					else $li.show();
				});
			});
			controls.find("a.nr_treeselect-checkall").click(function() {
				list.find("input").prop("checked", true);
			});
			controls.find("a.nr_treeselect-uncheckall").click(function() {
				list.find("input").prop("checked", false);
			});
			controls.find("a.nr_treeselect-toggleall").click(function() {
				list.find("input").each(function() {
					var $input = $(this);
					if ($input.prop("checked")) $input.prop("checked", false);
					else $input.prop("checked", true);
				});
			});
			controls.find("a.nr_treeselect-expandall").click(function() {
				list.find("ul.nr_treeselect-sub").show();
				list.find("span.nr_treeselect-toggle").removeClass("icon-plus").addClass("icon-minus");
			});
			controls.find("a.nr_treeselect-collapseall").click(function() {
				list.find("ul.nr_treeselect-sub").hide();
				list.find("span.nr_treeselect-toggle").removeClass("icon-minus").addClass("icon-plus");
			});
			controls.find("a.nr_treeselect-showall").click(function() {
				list.find("li").show();
			});
			controls.find("a.nr_treeselect-showselected").click(function() {
				list.find("li").each(function() {
					var $li = $(this);
					var $hide = true;
					$li.find("input").each(function() {
						if ($(this).prop("checked")) {
							$hide = false;
							return false;
						}
					});
					if ($hide) {
						$li.hide();
						return;
					}
					$li.show();
				});
			});
			controls.find("a.nr_treeselect-maximize").click(function() {
				list.css("max-height", "");
				controls.find("a.nr_treeselect-maximize").hide();
				controls.find("a.nr_treeselect-minimize").show();
			});
			controls.find("a.nr_treeselect-minimize").click(function() {
				list.css("max-height", maxheight);
				controls.find("a.nr_treeselect-minimize").hide();
				controls.find("a.nr_treeselect-maximize").show();
			});
		});
		$("div.nr_treeselect a.checkall").click(function() {
			$(this).parent().parent().parent().parent().parent().parent().find("ul.nr_treeselect-sub input").prop("checked",
				true);
		});
		$("div.nr_treeselect a.uncheckall").click(function() {
			$(this).parent().parent().parent().parent().parent().parent().find("ul.nr_treeselect-sub input").prop("checked", false);
		});
		$("div.nr_treeselect a.expandall").click(function() {
			var $parent = $(this).parent().parent().parent().parent().parent().parent().parent();
			$parent.find("ul.nr_treeselect-sub").show();
			$parent.find("ul.nr_treeselect-sub span.nr_treeselect-toggle").removeClass("icon-plus").addClass("icon-minus");
		});
		$("div.nr_treeselect a.collapseall").click(function() {
			var $parent =
				$(this).parent().parent().parent().parent().parent().parent().parent();
			$parent.find("li ul.nr_treeselect-sub").hide();
			$parent.find("li span.nr_treeselect-toggle").removeClass("icon-minus").addClass("icon-plus");
		});
		$("div.nr_treeselect-item.hidechildren").click(function() {
			var $parent = $(this).parent();
			$(this).find("input").each(function() {
				var $sub = $parent.find("ul.nr_treeselect-sub").first();
				var $input = $(this);
				if ($input.prop("checked")) {
					$parent.find("span.nr_treeselect-toggle, div.nr_treeselect-menu").css("visibility",
						"hidden");
					if (!$sub.parent().hasClass("hidelist")) $sub.wrap('<div style="display:none;" class="hidelist"></div>');
				} else {
					$parent.find("span.nr_treeselect-toggle, div.nr_treeselect-menu").css("visibility", "visible");
					if ($sub.parent().hasClass("hidelist")) $sub.unwrap();
				}
			});
		});
	});
})(jQuery);