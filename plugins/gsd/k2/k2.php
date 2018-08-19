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
 *  K2 Google Structured Data Plugin
 */
class plgGSDK2 extends GSDPlugin
{
    /**
     *  Database table name holds the items
     *
     *  @var  string
     */
	protected $table = "k2_items";

	/**
	 *  State Column Name
	 *
	 *  @var  string
	 */
	protected $column_state = 'published';

	/**
	 *  Get items's data
	 *
	 *  @return  array
	 */
	public function viewItem()
	{
		// Skip front-end editing page
		if (in_array($this->app->input->get('task'), array('edit', 'add')))
		{
			return;
		}

		// Load current item via model
		$model = JModelLegacy::getInstance('Item', 'K2Model');
		$item  = $model->getData();

		if (!is_object($item))
		{
			return;
		}

		// Prepare the item in order to get the item's images.
		$item = $model->prepareItem($item, "item", "");

		// Image Size. Defaults to large.
		$size_ = $this->params->get("imagesize", "large");
		$size_ = (substr($size_, 0, 1) == 'x') ? strtoupper(substr($size_, 0, 2)) . substr($size_, 2) : ucfirst($size_);
		$image = $item->{'image' . $size_};

        // Calculate rating
        $ratingValue = isset($item->votingPercentage) ? number_format($item->votingPercentage * 5 / 100, 1) : 0;
        $reviewCount = isset($item->numOfvotes) ? preg_replace("/[^0-9]/","", $item->numOfvotes) : 0;

		// Array data
		return array(
			"headline"    => $item->title,
			"description" => isset($item->introtext) && !empty($item->introtext) ? $item->introtext : $item->fulltext,
			"image"       => $image,
			"created_by"  => $item->created_by,
			"created"     => $item->created,
			"modified"    => $item->modified,
			"publish_up"  => $item->publish_up,
			"ratingValue" => $ratingValue,
        	"reviewCount" => $reviewCount
		);
	}

	/**
	 *  Prepare form to be added to the K2 item editing page.
	 *
	 *  @param   JForm  $form  The K2 Table Item
	 *
	 *  @return  object
	 */
	public function onGSDPluginForm($form)
	{
		// Only if fast edit is enabled
		if (!(bool) $this->params->get("fastedit", false))
		{
			return;
		}

		// Make sure the user can access com_gsd
		if (!JFactory::getUser()->authorise('core.manage', 'com_gsd'))
		{
			return;
		}

		// Make sure we are manipulating a K2 table object
		if (!($form instanceof TableK2Item))
		{
			return;
		}

		// Prepare our form
        $form = new JForm('form');
        $form->loadFile(__DIR__ . '/form/form.xml', false);
		$form->setFieldAttribute('snippet', 'thing', $this->app->input->getInt('cid'), 'plugins.gsd');
		$form->setFieldAttribute('snippet', 'plugin', $this->_name, 'plugins.gsd');

		// Oohh boy. Modals look messy due to K2 overrides. Let's fix it.
        JFactory::getDocument()->addStyleDeclaration('
			.itemPlugins {
			    font-family: Arial;
			    margin-top: 0;
			}
			.itemPlugins fieldset {
			    padding-top: 0 !important;
			}
			#gsdModal .modal-header h3 {
			    font-size: 18px !important;
			    padding: 0 !important;
			    color: inherit !important;
			    border:none !important;
			}
        ');

        // Prepare required K2 object
        $plugin = new stdClass();
        $plugin->name   = JText::_('GSD');
        $plugin->fields = $form->renderFieldset("gsd");

        return $plugin;
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
		$query->where($this->db->quoteName('a.trash') . ' =  0');

		return $query;
	}
}
