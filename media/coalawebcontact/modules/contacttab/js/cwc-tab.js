/**
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 */

jQuery.noConflict();
(function ($) {
    $(document).ready(function () {
        $(".cwc-contact-tab.left").css("left", "0px");
        $("#cwc-slideout.left").css("left", "-280px");
        $(".cwc-contact-tab.right").css("right", "0px");
        $("#cwc-slideout.right").css("right", "-280px");
    });
    $(function () {
        $('.cwc-contact-tab.left').toggle(function () {
            $(this).parent().animate({"left": "0px"}, 400); // Slide it out
            $(this).animate({"left": "280px"}, 400); // Slide it out
            $('.cwc-contact-tab a').addClass("active"); // add the active class to the link
            return false; // remove the default link behaviour
        }, function () {
            $(this).parent().stop().animate({"left": "-280px"}, 400); // Slide it back in
            $(this).stop().animate({"left": "0px"}, 400); // Slide it out
            $('.cwc-contact-tab a').removeClass("active"); // remove active class
            return false; // remove the default link behaviour
        });
        $('.cwc-contact-tab.right').toggle(function () {
            $(this).parent().animate({"right": "0px"}, 400); // Slide it out
            $(this).animate({"right": "280px"}, 400); // Slide it out
            $('.cwc-contact-tab a').addClass("active"); // add the active class to the link
            return false; // remove the default link behaviour
        }, function () {
            $(this).parent().stop().animate({"right": "-280px"}, 400); // Slide it back in
            $(this).stop().animate({"right": "0px"}, 400); // Slide it out
            $('.cwc-contact-tab a').removeClass("active"); // remove active class
            return false; // remove the default link behaviour
        });

    });
})(jQuery);


