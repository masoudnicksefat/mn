<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Header Element
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/gpl.html>.
 */

JFormHelper::loadFieldClass('list');

class JFormFieldHelpfix extends JFormFieldList
{
	public $type = 'Helpfix';

	
	/*
	 * This has been moved to the form field csscom
	 */
	protected function getInput()
	{
		$html = parent::getInput();
		
		if(JFactory::getApplication()->input->getCmd('option', '') == 'com_config')
		{
			JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function($) {
					$("label[for=\'jform_help_fix\']").closest(".control-group").remove()
					
				});
			');
		}
		return $html;
	}
}
