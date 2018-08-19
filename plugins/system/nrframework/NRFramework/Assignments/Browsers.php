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

class Browsers extends Assignment
{
    /**
     *  Check the client's browser
     *
     *  @return bool
     */
    function passBrowsers()
    {
        return $this->passSimple(WebClient::getBrowser()['name'], $this->selection);
    }
}
