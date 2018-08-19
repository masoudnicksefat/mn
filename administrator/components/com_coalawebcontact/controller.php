
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
// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller for coalawebcontact component
 */
class CoalawebcontactController extends JControllerLegacy {

    protected $default_view = 'Controlpanel';

    /**
     * Method to display a view.
     *
     * @param	boolean
     * @param	array
     *
     * @return	JController
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {

        require_once JPATH_COMPONENT . '/helpers/coalawebcontact.php';

        $view = JFactory::getApplication()->input->get('view', 'Controlpanel');
        $layout = JFactory::getApplication()->input->get('layout', 'default');


        parent::display();
        return $this;
    }

}
