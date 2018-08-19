/*! UIkit 2.27.1 | http://www.getuikit.com | (c) 2014 YOOtheme | MIT License */
(function(UI) {

    "use strict";

    UI.component('nav', {

        defaults: {
            toggle: ">li.cw-parent > a[href='#']",
            lists: ">li.cw-parent > ul",
            multiple: false
        },

        boot: function() {

            // init code
            UI.ready(function(context) {

                UI.$("[data-cw-nav]", context).each(function() {
                    var nav = UI.$(this);

                    if (!nav.data("nav")) {
                        var obj = UI.nav(nav, UI.Utils.options(nav.attr("data-cw-nav")));
                    }
                });
            });
        },

        init: function() {

            var $this = this;

            this.on("click.uk.nav", this.options.toggle, function(e) {
                e.preventDefault();
                var ele = UI.$(this);
                $this.open(ele.parent()[0] == $this.element[0] ? ele : ele.parent("li"));
            });

            this.update(true);

            UI.domObserve(this.element, function(e) {
                if ($this.element.find(this.options.lists).not('[role]').length) {
                    $this.update();
                }
            });
        },

        update: function(init) {

            var $this = this;

            this.find(this.options.lists).each(function() {

                var $ele   = UI.$(this).attr('role', 'menu'),
                    parent = $ele.closest('li'),
                    active = parent.hasClass("cw-active");

                if (!parent.data('list-container')) {
                    $ele.wrap('<div style="overflow:hidden;height:0;position:relative;"></div>');
                    parent.data('list-container', $ele.parent()[active ? 'removeClass':'addClass']('cw-hidden'));
                }

                // Init ARIA
                parent.attr('aria-expanded', parent.hasClass("cw-open"));

                if (active) $this.open(parent, true);
            });
        },

        open: function(li, noanimation) {

            var $this = this, element = this.element, $li = UI.$(li), $container = $li.data('list-container');

            if (!this.options.multiple) {

                element.children('.cw-open').not(li).each(function() {

                    var ele = UI.$(this);

                    if (ele.data('list-container')) {
                        ele.data('list-container').stop().animate({height: 0}, function() {
                            UI.$(this).parent().removeClass('cw-open').end().addClass('cw-hidden');
                        });
                    }
                });
            }

            $li.toggleClass('cw-open');

            // Update ARIA
            $li.attr('aria-expanded', $li.hasClass('cw-open'));

            if ($container) {

                if ($li.hasClass('cw-open')) {
                    $container.removeClass('cw-hidden');
                }

                if (noanimation) {

                    $container.stop().height($li.hasClass('cw-open') ? 'auto' : 0);

                    if (!$li.hasClass('cw-open')) {
                        $container.addClass('cw-hidden');
                    }

                    this.trigger('display.uk.check');

                } else {

                    $container.stop().animate({
                        height: ($li.hasClass('cw-open') ? getHeight($container.find('ul:first')) : 0)
                    }, function() {

                        if (!$li.hasClass('cw-open')) {
                            $container.addClass('cw-hidden');
                        } else {
                            $container.css('height', '');
                        }

                        $this.trigger('display.uk.check');
                    });
                }
            }
        }
    });


    // helper

    function getHeight(ele) {

        var $ele = UI.$(ele), height = "auto";

        if ($ele.is(":visible")) {
            height = $ele.outerHeight();
        } else {
            var tmp = {
                position: $ele.css("position"),
                visibility: $ele.css("visibility"),
                display: $ele.css("display")
            };

            height = $ele.css({position: 'absolute', visibility: 'hidden', display: 'block'}).outerHeight();

            $ele.css(tmp); // reset element
        }

        return height;
    }

})(UIkit);
