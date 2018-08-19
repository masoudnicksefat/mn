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
 *  Zoo Google Structured Data Plugin
 */
class plgGSDZoo extends GSDPlugin
{
    /**
     *  Database table name holds the items
     *
     *  @var  string
     */
	protected $table = 'zoo_item';

    /**
     *  Title column name
     *
     *  @var  string
     */
    protected $column_title = 'name';

    /**
     *  Current manipulated item
     *
     *  @var  object
     */
    private $item;

    /**
     *  Discover Zoo view name
     *
     *  The 'view' parameter is used only if the Zoom item is assosiated to a menu item.
     *  Otherwise it uses the 'task' parameter. That's why we override this method.
     *
     *  @return  string  The view name
     */
    protected function getView()
    {
    	$input = $this->app->input;

    	if ($input->get('view') == 'item' || $input->get('task') == 'item')
    	{
    		return 'item';
    	}

    	return $input->get('view');
    }

    /**
     *  Discover Zoo Item ID
     *  
     *  Normally the item's id can be read by the request parameters BUT if the item
     *  is assosiated to a menu item the item_id parameter is not yet available and 
     *  we can only find it out through the menu's parameters.
     *  
     *  @return  integer   The item's ID
     */
    protected function getThingID()
    {
    	$requestID = $this->app->input->getInt('item_id', null);

    	if (!is_null($requestID))
    	{
    		return $requestID;
	   	}

	   	// Try to discover the item id from the menu parameters
        return (int) $this->app->getMenu()->getActive()->params->get("item_id");
    }

	/**
	 *  Get the post's data
	 *
	 *  @return  array
	 */
	public function viewItem()
	{
		// Make sure Zoo App is available
		if (!class_exists('App'))
		{
			return;
		}

		$zoo = App::getInstance('zoo');

		if (!$this->item = $zoo->table->item->get($this->getThingID()))
		{
			return;
		}

		// Return default data merged with the rating info
		return array_merge(
			array(
				'headline'    => $this->item->name,
				'description' => $this->getItemDescription(),
				'image'       => $this->getImage(),
				'created_by'  => $this->item->created_by,
				'created'     => $this->item->created,
				'modified'    => $this->item->modified,
				'publish_up'  => $this->item->publish_up
			), 
			$this->getItemRating()
		);
	}

	/**
	 *  Get Zoo Item's Description
	 *
	 *  @return  mixed  Null on failure, String on success
	 */
	private function getItemDescription()
	{
		$text = $this->getElement('textarea', $this->params->get('text_element'));

		if (!is_object($text))
		{
			return;
		}

		return $text->data()[0]['value'];
	}

	/**
	 *  Get Zoo Item's image
	 *
	 *  @return  string  The image filename path
	 */
	private function getImage()
	{
		$teaser = $this->getElement('image', $this->params->get('image_element'));

		if (!is_object($teaser))
		{
			return;
		}

		return $teaser->get('file');
	}

	/**
	 *  Get Zoo Item's Rating value and review's counter
	 *
	 *  @return  mixed  Null on failure, Array on success
	 */
	private function getItemRating()
	{
		$rating = $this->getElement('rating');

		if (!$rating || is_null($rating))
		{
			return array();
		}

		return array(
			'ratingValue' => $rating->getRating(),
			'reviewCount' => (int) $rating->get('votes', 0),
			'bestRating'  => (int) $rating->config->get('stars', 5)
		);
	}

	/**
	 *  Finds any Item's element by type or name
	 *
	 *  @param   string  $type  Element type name
	 *  @param   string  $name  Element name
	 *
	 *  @return  object         The found element object
	 */
	private function getElement($type = 'image', $name = null)
	{
		if (!is_array($this->item->getElements()))
		{
			return;
		}

		foreach ($this->item->getElements() as $element)
		{
			if ($element->getElementType() != $type)
			{
				continue;
			}

			if (!is_null($name) && $element->config->get('name') != $name)
			{
				continue;
			}

			return $element;
		}
	}
}
