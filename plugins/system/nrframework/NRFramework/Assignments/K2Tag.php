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

class K2Tag extends K2
{
    /**
     *  Pass check for K2 Tags
     *
     *  @return bool
     */
    public function passK2Tag()
    {
        // Check we are on the right context and we have a valid Item ID
        if (empty($this->selection) || !$this->passContext() || !$id = $this->getItemID())
        {
            return false;
        }
        
        $q = $this->db->getQuery(true)
            ->select('t.id')
            ->from('#__k2_tags_xref AS tx')
            ->join('LEFT', '#__k2_tags AS t ON t.id = tx.tagID')
            ->where('tx.itemID = ' . $id)
            ->where('t.published = 1');

		$this->db->setQuery($q);
        $tags = $this->db->loadColumn();
        
        return $this->passSimple($tags, $this->selection);
    }
}
