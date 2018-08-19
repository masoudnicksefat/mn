<?php

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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT .  '/tables');

/**
 * Methods supporting a control panel
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebcontact
 */
class CoalawebcontactModelControlpanel extends JModelLegacy {
    
    /**
     * Check if a download ID is needed (Pro versions)
     * 
     * @return boolean
     */
    public function needsDownloadID() {
        // Do I need a Download ID?
        $ret = true;
        $isPro = defined('COM_CWCONTACT_PRO') ? COM_CWCONTACT_PRO : 0;
        if (!$isPro) {
            $ret = false;
        } else {
            jimport('joomla.application.component.helper');
            $componentParams = JComponentHelper::getParams('com_coalawebcontact');
            $dlid = $componentParams->get('downloadid');
            
            if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid)) {
                $ret = false;
            }
        }

        return $ret;
    }

}
