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

class Menu extends Assignment 
{
	/**
	 *  Pass check for menu items
	 *
	 *  @return  bool
	 */
	function passMenu()
	{
		$includeChildren = isset($this->params->inc_children) ? $this->params->inc_children : false;
    	$includeNoItemID = isset($this->params->noitem) ? $this->params->noitem : false;
    	// Pass if selection is empty or the itemid is missing
    	if (!$this->request->Itemid || empty($this->selection))
        {
        	return $includeNoItemID;
        }

        // return true if menu type is in selection
		$menutype = 'type.' . $this->getMenuType();
		if (in_array($menutype, $this->selection))
		{
			return true;
		}

		// return true if menu is in selection and we are not including child items only
		if (in_array($this->request->Itemid, $this->selection))
		{
			return ($includeChildren != 2);
		}

		// Let's discover child items. 
		// Obviously if the option is disabled return false.
		if (!$includeChildren)
		{
			return false;
		}

		// Get menu item parents
		$parent_ids = $this->getParentIds($this->request->Itemid);
		$parent_ids = array_diff($parent_ids, array('1'));

		foreach ($parent_ids as $id)
		{
			if (!in_array($id, $this->selection))
			{
				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 *  Get active menu items's menu type
	 *
	 *  @return  bool   False on failure, string on success
	 */
	private function getMenuType()
	{
		if (empty($this->request->Itemid))
		{
			return;
		}

		$menu = $this->app->getMenu()->getItem((int) $this->request->Itemid);

		return isset($menu->menutype) ? $menu->menutype : false;
	}
}
