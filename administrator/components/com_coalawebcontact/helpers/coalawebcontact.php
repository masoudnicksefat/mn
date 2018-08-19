<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Component
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  component helper.
 */
abstract class CoalawebcontactHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = 'controlpanel') {
        JHtmlSidebar::addEntry(
                JText::_('COM_CWCONTACT_TITLE_CPANEL'), 'index.php?option=com_coalawebcontact&view=controlpanel', $vName == 'controlpanel');
        JHtmlSidebar::addEntry(
            JText::_('COM_CWCONTACT_VIEW_CUSTOMFIELDS_TITLE'), 'index.php?option=com_coalawebcontact&view=customfields', $vName == 'customfields');
        JHtmlSidebar::addEntry(
            JText::_('COM_CWCONTACT_VIEW_EMAILTEMPLATES_TITLE'), 'index.php?option=com_coalawebcontact&view=emailtemplates', $vName == 'emailtemplates');
    }

    
    /**
     * Get module list in text/value format for a select field
     *
     * @return  array
     */
    public static function getModuleOptions() {
    $options = array();

    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
            ->select('id As value, id As text')
            ->from('#__modules AS a')
            ->where('a.module = ' . $db->q('mod_coalawebcontact'), 'OR')
            ->where('a.module = ' . $db->q('mod_coalawebcontacttab'))
            ->order('a.id');

    // Get the options.
    $db->setQuery($query);

    try {
        $options = $db->loadObjectList();
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), 500);
    }

    array_unshift($options, JHtml::_('select.option', '*', JText::_('COM_CWCONTACT_ALL_MODULES')));

    return $options;
    }

}
