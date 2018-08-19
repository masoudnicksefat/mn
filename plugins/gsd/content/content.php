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

use Joomla\Registry\Registry;

/**
 *  Joomla! Content Google Structured Data Plugin
 *
 *  Note: Content component produces its own microdata.
 */
class plgGSDContent extends GSDPlugin
{
	/**
	 *  Get article's data
	 *
	 *  @return  array
	 */
	public function viewArticle()
	{
		// Load current item via model
		$model = JModelLegacy::getInstance('Article', 'ContentModel');
		$item  = $model->getItem();

		// Image
		$image = new Registry($item->images);

		// Array data
		return array(
			"headline"    => $item->title,
			"description" => isset($item->introtext) && !empty($item->introtext) ? $item->introtext : $item->fulltext,
			"image"       => $image->get("image_intro") ?: $image->get("image_fulltext"),
			"created_by"  => $item->created_by,
			"created"     => $item->created,
			"modified"    => $item->modified,
			"publish_up"  => $item->publish_up,
			"ratingValue" => $item->rating,
        	"reviewCount" => $item->rating_count
		);
	}

	/**
	 *  Prepare form and a new tab in the article editing page
	 *
	 *  @param   JForm  $form  The form to be altered.
	 *  @param   mixed  $data  The associated data for the form.
	 *
	 *  @return  boolean
	 */
	function onGSDPluginForm($form, $data)
	{
		// Only if fast edit is enabled
		if (!(bool) $this->params->get("fastedit", true))
		{
			return;
		}

		// Make sure the user can access com_gsd
		if (!JFactory::getUser()->authorise('core.manage', 'com_gsd'))
		{
			return;
		}

		// Make sure we are manipulating a JForm
		if (!($form instanceof JForm))
		{
			return;
		}

		if ($form->getName() != 'com_content.article')
		{
			return;
		}

		$form->loadFile(__DIR__ . '/form/form.xml', false);
		$form->setFieldAttribute('snippet', 'thing', $this->app->input->getInt('id'), 'attribs.gsd');
		$form->setFieldAttribute('snippet', 'plugin', $this->_name, 'attribs.gsd');
	}

    /**
     *  Construct the query needed for the item selection modal in the backend.
     *  
     *  Returns all items but trashed
     *
     *  @param   JModel  $model  The Things Model
     *
     *  @return  Query
     */
	protected function getListQuery($model)
	{
		$query = parent::getListQuery($model);
		$query->where($this->db->quoteName('a.' . $this->getColumn('state')) . ' >= 0');

		return $query;
	}
}
