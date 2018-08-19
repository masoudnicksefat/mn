<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Assignments;

defined('_JEXEC') or die;

use NRFramework\Assignment;
use NRFramework\WebClient;

class OS extends Assignment
{
    /**
     *  Check the client's operating system
     *
     *  @return bool
     */
    function passOS()
    {
        // backwards compatibility check
        // replace 'iphone' and 'ipad' selection values with 'ios'
        $this->selection = array_map(function($os_selection)
        {
            if ($os_selection === 'iphone' || $os_selection === 'ipad')
            {
                return 'ios';
            }
            return $os_selection;
        },
        $this->selection);

        return $this->passSimple(WebClient::getOS(), $this->selection);
    }
}
