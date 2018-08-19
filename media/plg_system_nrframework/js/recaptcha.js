/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

window.NRInitReCaptcha = function() {
	'use strict';

	var items = document.getElementsByClassName('nr-recaptcha'), 
		item, 
		options;

	for (var i = 0, l = items.length; i < l; i++) {

		item = items[i];

		options = item.dataset ? item.dataset : {
			sitekey: item.getAttribute('data-sitekey'),
			theme:   item.getAttribute('data-theme'),
			size:    item.getAttribute('data-size')
		};

		var widgetID = grecaptcha.render(item, options);

		// Add the widget ID to element to enable widget manipulation by other scripts.
		item.setAttribute('data-widgetid', widgetID);
	}
}
