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
use NRFramework\Assignments\K2;

class K2Pagetype extends K2
{
    /**
     *  Pass check for K2 page types
     *
     *  @return bool
     */
    public function passK2Pagetype()
    {
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
        }

        $pagetype = $this->request->view . '_' . ($this->request->layout !== '' ? $this->request->layout : $this->request->view);
        return $this->passSimple($pagetype, $this->selection);
    }
}
