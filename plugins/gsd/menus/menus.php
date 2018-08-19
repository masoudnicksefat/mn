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

JLoader::register('GSDPlugin', JPATH_ADMINISTRATOR . '/components/com_gsd/helpers/plugin.php');

/**
 *  Add Google Structured Data to Menu Items
 *
 *  Notes:
 *  - Configurable Author / Disable Inherit
 */
class plgGSDMenus extends GSDPlugin
{
	/**
	 *  Active Menu Item
	 *
	 *  @var  object
	 */
	protected $menu;

    /**
     *  Database table name to search for things
     *
     *  @var  string
     */
    protected $table = 'menu';

    /**
     *  State column name
     *
     *  @var  string
     */
    protected $column_state = 'published';

    /**
     *  Validate context to decide whether the plugin should run or not.
     *
     *  @return   bool
     */
    protected function passContext()
    {
    	$this->menu = $this->app->getMenu()->getActive();

		if (is_object($this->menu) && isset($this->menu->id))
		{
			return true;
		}

		return false;
    }

    /**
     *  Get Item's ID
     *
     *  @return  string
     */
    protected function getThingID()
    {
        return $this->menu->id;
    }

    /**
     *  Discover view name
     *
     *  @return  string  The view name
     */
    protected function getView()
    {
    	return $this->_name;
    }

    /**
     *  Get Menu Item Payload
     *
     *  @return  array   The Menu Item Payload array
     */
	public function viewMenus()
	{
		return array(
			"headline"    => $this->menu->params->get('page_title', $this->menu->title),
			"description" => $this->menu->params->get('menu-meta_description'),
			"image"       => $this->menu->params->get('menu_image')
		);
	}

	/**
     *  Construct the query needed for the item selection modal in the backend.
     *
     *  @param   JModel  $model  The Things Model
     *
     *  @return  Query
     */
    protected function getListQuery($model)
    {
    	$query = parent::getListQuery($model);

    	$query
    		->where($this->db->quoteName('client_id') . ' = 0')
    		->where($this->db->quoteName('parent_id') . ' > 0')
    		->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
            ->where($this->db->quoteName('a.' . $this->getColumn('state')) . ' >= 0');

    	return $query;
    }

	/**
	 *  Route default form's prepare event to onGSDPluginForm to help our plugins manipulate the form
	 *
	 *  @param   JForm  $form  The form to be altered.
	 *  @param   mixed  $data  The associated data for the form.
	 *
	 *  @return  boolean
	 */
	public function onGSDPluginForm($form, $data)
	{
		// Run only on backend
		if (!$this->app->isAdmin() || !$form instanceof JForm)
		{
			return;
		}

		// Add a new tab called 'Google Structured Data' in the Menu Manager Item editing page
		// only on component-based menu items. System links such as as URLs or Menu Item Aliases are not supported.
		if ($form->getName() == 'com_menus.item' && $data['component_id'] > 0 && $this->params->get("fastedit", false))
		{
			$form->loadFile(__DIR__ . '/form.xml', false);
			$form->setFieldAttribute('snippet', 'thing', $this->app->input->getInt('id'), 'params.gsd');

			return;
		}

		// Since we are not able to dynamically assosiate a menu item with a component-based item
        // we need to disable the Inherit option of all Rating and Author fields.
		if ($form->getName() == 'com_gsd.item' && $data->plugin == 'menus')
		{
			foreach (GSDHelper::getContentTypes() as $contentType)
			{
                $form->setFieldAttribute('rating', 'hideinherit', true, $contentType);
                $form->setFieldAttribute('author', 'hideinherit', true, $contentType);
			}
		}
	}
}
