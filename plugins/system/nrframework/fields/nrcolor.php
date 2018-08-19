<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('color');

/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's `<input type="color">`
 */

class JFormFieldNRColor extends JFormFieldColor
{
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		$this->colors = array(
			'#eee',
			'#333',
			'#ef2345',
			'#2379ef',
			'#2ec664',
			'#ee7a38',
			'#eed938'
		);

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.3
	 */
	protected function getInput()
	{
		if (version_compare(JVERSION, '4.0', 'ge'))
		{
			$this->colors = implode(',', $this->colors);
			return parent::getInput();
		}

		$lang = JFactory::getLanguage();

		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Control value can be: hue (default), saturation, brightness, wheel or simple
		$control = $this->control;

		// Position of the panel can be: right (default), left, top or bottom (default RTL is left)
		$position = ' data-position="' . (($lang->isRTL() && $this->position == 'default') ? 'left' : $this->position) . '"';

		// Validation of data can be: color (hex color value). Keep for B/C (minicolors.js already auto-validates color)
		$validate = $this->validate ? ' data-validate="' . $this->validate . '"' : '';

		$onchange  = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';
		$class     = $this->class;
		$required  = $this->required ? ' required aria-required="true"' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';

		$color = strtolower($this->value);
		$color = ! $color ? '' : $color;

		if (in_array($this->format, array('rgb', 'rgba')) && $this->validate != 'color')
		{
			$alpha = ($this->format == 'rgba') ? true : false;
			$placeholder = $alpha ? 'rgba(0, 0, 0, 0.5)' : 'rgb(0, 0, 0)';
		}
		else
		{
			$placeholder = '#rrggbb';
		}

		$inputclass   = ($this->keywords && ! in_array($this->format, array('rgb', 'rgba'))) ? ' keywords' : ' ' . $this->format;
		$class        = ' class="' . trim('nrcolor ' . $class) . ($this->validate == 'color' ? '' : $inputclass) . '"';
		$control      = $control ? ' data-control="' . $control . '"' : '';
		$format       = $this->format ? ' data-format="' . $this->format . '"' : '';
		$keywords     = $this->keywords ? ' data-keywords="' . $this->keywords . '"' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$hint         = strlen($hint) ? ' placeholder="' . $hint . '"' : ' placeholder="' . $placeholder . '"';
		$autocomplete = ! $this->autocomplete ? ' autocomplete="off"' : '';

		// Force LTR input value in RTL, due to display issues with rgba/hex colors
		$direction    = $lang->isRTL() ? ' dir="ltr" style="text-align:right"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');

		$doc = JFactory::getDocument();

        if (version_compare(JVERSION, '3.6', 'l'))
        {
        	// On Joomla version < 3.6 the minicolors library is outdated. So we load the latest version from joomla.org
        	// This check should be removed after Joomla 4.0 is out.
        	$doc->addStyleSheet('//joomla.org/media/jui/css/jquery.minicolors.css');
       		$doc->addScript('//joomla.org/media/jui/js/jquery.minicolors.min.js', false, true);       	
        } else {
        	$doc->addStyleSheet(JURI::root(true).'/media/jui/css/jquery.minicolors.css?v=1');
        	$doc->addScript(JURI::root(true).'/media/jui/js/jquery.minicolors.min.js?v=1', false, true);  	
        }

        static $run;

        // Run once
        if (!$run)
        {
 			$doc->addScriptDeclaration('
				jQuery(function($) {
					$(".nrcolor").each(function() {
						$(this).minicolors({
							control: $(this).attr("data-control") || "hue",
							format: $(this).attr("data-validate") === "color"
								? "hex"
								: ($(this).attr("data-format") === "rgba"
									? "rgb"
									: $(this).attr("data-format"))
								|| "hex",
							keywords: $(this).attr("data-keywords") || "",
							opacity: $(this).attr("data-format") === "rgba" ? true : false || false,
							position: $(this).attr("data-position") || "default",
							swatches: $(this).attr("data-swatches") ? $(this).attr("data-swatches").split("|") : [],
							theme: "bootstrap"
						});
					});
				});
        	');

			$doc->addStyleDeclaration('
				.nrcolor.rgb, .nrcolor.rgba {
				    font-size: 13px;
				    min-width: 185px;
				    max-width: 185px;
				}
				.minicolors-swatches > li {
				    left: auto !important;
				    position: relative !important;
				    top: auto !important;
				}
				.minicolors-swatches {
				    box-sizing: border-box;
				    width: 100%;
				    background-color: #ffffff;
				    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
				    display: flex;
				    justify-content:space-around;
				    display: -webkit-flex;
				    -webkit-justify-content:space-around;
				    left: 0;
				    margin: 0;
				    padding: 3px 5px 6px;
				    position: absolute;
				    top: 157px;
				    border-radius: 5px;
				}
				.minicolors-grid .minicolors-picker {
				    mix-blend-mode: difference;
				    border:solid 2px #fff;
				}
			');

 			$run = true;
        }

		return '<input data-swatches="' . implode('|', $this->colors) . '" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($color, ENT_COMPAT, 'UTF-8') . '"' . $hint . $class . $position . $control
			. $readonly . $disabled . $required . $onchange . $autocomplete . $autofocus
			. $format . $keywords . $direction . $validate . '/>';
	}
}
