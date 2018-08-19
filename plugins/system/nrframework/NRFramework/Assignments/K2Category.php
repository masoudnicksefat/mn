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

class K2Category extends K2
{
    /**
     *  Pass check for K2 categories
     *
     *  @return bool
     */
    public function passK2Category()
    {
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
        }

		$inc_categories = true;
		$inc_items      = true;
		$inc_children   = $this->params->inc_children;
		$is_category    = $this->isCategory();
		$is_item        = $this->isItem();

		if (isset($this->params->inc) && is_array($this->params->inc))
		{
			$inc_categories = in_array('inc_categories', $this->params->inc);
			$inc_items      = in_array('inc_items', $this->params->inc);
		}

		// Check if we are in a valid context
		if (!($inc_categories && $is_category) && !($inc_items && $is_item))
		{
			return false;
		}

		$pass = false;
		$catids = $this->getCategoryIds();

		foreach ($catids as $catid)
		{
			if (!$catid)
			{
				continue;
			}

			$pass = in_array($catid, $this->selection);

			// Pass check on child items only
			if ($pass && $this->params->inc_children == 2)
			{
				$pass = false;
				continue;
			}

			// Pass check for child items
			if (!$pass && $this->params->inc_children)
			{
				$parent_ids = $this->getParentIDs($catid, 'k2_categories', 'parent');
				foreach ($parent_ids as $id)
				{
					if (in_array($id, $this->selection))
					{
						$pass = true;
						break;
					}
				}
				unset($parent_ids);
			}
		}

		return $pass;
    }

    /**
	 *  Returns category IDs based on the active K2 view
	 *
	 *  @return  array                  The IDs
	 */
	protected function getCategoryIds()
	{
		// If we are in category view return category's id
		if ($this->isCategory())
		{
			// Note: If the category alias starts with a number then we end up with a wrong result
			$catid = (int) $this->request->id;
			return (array) $catid;
		}

		// If we are in article view return article's category id
		if ($item = $this->getK2Item())
		{
            if (isset($item->catid))
            {
                return (array) $item->catid;
            }
		}

		return false;
    }
}
