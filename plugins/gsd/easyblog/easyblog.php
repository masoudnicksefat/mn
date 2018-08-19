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
 *  EasyBlog Google Structured Data Plugin

 *  It produces the Articles snippet using microdata
 *
 *  Developer References:
 *  components\com_easyblog\themes\wireframe\blogs\entry\default.php
 */
class plgGSDEasyBlog extends GSDPlugin
{
    /**
     *  Database table name holds the items
     *
     *  @var  string
     */
	protected $table = "easyblog_post";
	
	/**
	 *  State column name
	 *
	 *  @var  string
	 */
	protected $column_state = "published";

    /**
     *  Validate context to decide whether the plugin should run or not.
     *  Disable when the article is on Preview Mode.
     *
     *  @return   bool
     */
    protected function passContext()
    {
    	if ($this->app->input->get('layout') == 'preview')
    	{
    		return;
    	}

    	return parent::passContext();
    }

	/**
	 *  Get the post's data
	 *
	 *  @return  array
	 */
	public function viewEntry()
	{
		// Abort if EasyBlog's main class is missing
		if (!class_exists("EB"))
		{
			return;
		}

		// Load EasyBlog config
		$config = EB::config();

		// Make sure we have a valid ID
		if (!$id = $this->getThingID())
		{
			return;
		}
		
		// Load EasyBlog post
		$post = EB::post($id);

		// Ratings
		$rating = $post->getRatings();

		// Array data
		return array(
			"headline"    => $post->title,
			"description" => $post->getIntro() ?: $post->content,
			"image"       => $post->getImage($config->get('cover_size_entry', 'large'), true, true, false),
			"created_by"  => $post->created_by,
			"created"     => $post->created,
			"modified"    => $post->modified,
			"publish_up"  => $post->publish_up,
			"ratingValue" => number_format($rating->ratings / 2, 1), // EasyBlog's best rating value is 10
        	"reviewCount" => $rating->total
		);
	}
}
