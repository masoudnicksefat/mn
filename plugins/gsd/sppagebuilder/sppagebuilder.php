<?php

/**
 * @package         Google Structured Data
 * @version         3.1.7 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_gsd/helpers/plugin.php';

/**
 *  SP Page Builder Google Structured Data Plugin
 */
class plgGSDSPPageBuilder extends GSDPlugin
{
	/**
	 *  State column name
	 *
	 *  @var  string
	 */
	protected $column_state = 'published';

	/**
	 *  Get page's data
	 *
	 *  @return  array
	 */
	public function viewPage()
	{
		// Load current item via model
		$model = JModelLegacy::getInstance('Page', 'SppagebuilderModel');
		$item  = $model->getItem();

		// Array data
		return array(
			"headline"    => $item->title,
			"created_by"  => $item->created_by,
			"created"     => $item->created_on,
			"modified"    => $item->modified,
			"publish_up"  => $item->created_on
		);
	}
}
