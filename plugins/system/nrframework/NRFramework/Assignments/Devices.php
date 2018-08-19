<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Assignments;

defined('_JEXEC') or die;

use NRFramework\Assignment;
use NRFramework\WebClient;

class Devices extends Assignment
{
    /**
     *  Checks client's device type
     *
     *  @return  bool
     */
	function passDevices()
	{
    	return $this->passSimple(WebClient::getDeviceType(), $this->selection);
	}
}
