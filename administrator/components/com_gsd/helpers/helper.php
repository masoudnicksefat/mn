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

defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;

/**
 *  Google Structured Data Helper Class
 */
class GSDHelper
{
	/**
	 *  Plugin Params
	 *
	 *  @var  JRegistry
	 */
	public static $params;

	/**
	 *  Log Messages
	 *
	 *  @var  array
	 */
	public static $log;

	/**
	 *  Get all available Content Types
	 *
	 *  @return  array
	 */
	public static function getContentTypes()
	{
		JLoader::register('GSDJSON', JPATH_ADMINISTRATOR . '/components/com_gsd/helpers/json.php');
        $json = new GSDJSON();

        return $json->getContentTypes();
	}

	/**
	 *  Returns an array with crumbs
	 *
	 *  @return  array
	 */
	public static function getCrumbs($hometext)
	{
		$pathway = JFactory::getApplication()->getPathway();
		$items   = $pathway->getPathWay();
		$menu    = JFactory::getApplication()->getMenu();
		$lang    = JFactory::getLanguage();
		$count   = count($items);

		// Look for the home menu
		if (JLanguageMultilang::isEnabled())
		{
			$home = $menu->getDefault($lang->getTag());
		}
		else
		{
			$home = $menu->getDefault();
		}

		if (!$count)
		{
			return false;
		}

		// We don't use $items here as it references JPathway properties directly
		$crumbs = array();

		for ($i = 0; $i < $count; $i++)
		{
			$crumbs[$i]       = new stdClass;
			$crumbs[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
			$crumbs[$i]->link = self::route($items[$i]->link);
		}

		// Add Home item
		$item       = new stdClass;
		$item->name = htmlspecialchars($hometext);
		$item->link = self::route('index.php?Itemid=' . $home->id);
		array_unshift($crumbs, $item);

		// Fix last item's missing URL to make Google Markup Tool happy
		end($crumbs);
		if (empty($crumbs->link))
		{
			$crumbs[key($crumbs)]->link = JURI::current();
		}

		return $crumbs;
	}

	/**
	 *  Returns image width and height
	 *
	 *  @param   string  $image  The URL of the image2wbmp(image)
	 *
	 *  @return  array
	 */
	public static function getImageSize($image)
	{
		if (!ini_get('allow_url_fopen') || !function_exists('getimagesize'))
		{
			return array("width" => 0, "height" => 0);
		}

		$imageSize = $image ? getimagesize($image) : array(0, 0);

		$info["width"]  = $imageSize[0];
		$info["height"] = $imageSize[1];

		return $info;
	}

	/**
	 *  Makes text safe for JSON outpout
	 *
	 *  @param   string   $text   The text 
	 *  @param   integer  $limit  Limit characters
	 *
	 *  @return  string
	 */
	public static function makeTextSafe($text, $prepareContent = true, $limit = 0)
	{
		if (empty($text))
		{
			return;
		}

		if ($prepareContent)
		{
			$text = JHtml::_('content.prepare', $text);
		}

		// Strip HTML tags/comments and minify
		$text = strip_tags($text);

		// Strip certain patterns like shortcodes added by 3rd party extensions to avoid page breaks
		$text = preg_replace(array(
			'/{(\/?)zen-(.*?)}/m' // System - Zen Shortcodes
		), '', $text);
		 
		// Minify Text
		$text = self::minify($text);

		// Limit characters length
		if ($limit > 0)
		{
			$text = mb_substr($text, 0, $limit);
		}

		// Escape double quotes
       	$text = addcslashes($text, '"\\');

        return trim($text);
	}

	/**
	 *  Minify String
	 *
	 *  @param   string  $string  The string to be minified
	 *
	 *  @return  string           The minified string
	 */
	public static function minify($string)
	{
    	return preg_replace('/(\s)+/s', ' ', $string);
	}

	/**
	 *  Returns absolute URL
	 *
	 *  @param   string  $url  The URL
	 *
	 *  @return  string
	 */
	public static function absURL($url)
	{
		$url = JURI::getInstance($url);

		// Return the original URL if we're manipulating an external URL
		if (in_array($url->getScheme(), array('https', 'http')))
		{
			return $url->toString();
		}

		$url = str_replace(array(JURI::root(), JURI::root(true)), '', $url->toString());
		$url = ltrim($url, '/');
		
		return JURI::root() . $url;
	}

	/**
	 *  Returns URLs based on the Force SSL global configuration
	 *
	 *  @param   string   $route  The route for which we want a URL
	 *  @param   boolean  $xhtml  If we want the output to be in XHTML
	 *
	 *  @return  string           The absolute url
	 */
	public static function route($route, $xhtml = true)
	{
		$siteSSL = JFactory::getConfig()->get('force_ssl');
		$sslFlag = 2;

		// the force_ssl value in the global configuration needs
		// to be 2 for the frontend to also be under HTTPS
		if (($siteSSL == 2) || (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'))
		{
			$sslFlag = 1;
		}
		
		return JRoute::_($route, $xhtml, $sslFlag);
	}

	/**
	 *  Formats date based on ISO8601 including site's timezone set in the global configuration
	 *
	 *  @param   JDate  $date  
	 *
	 *  @return  JDate
	 */
	public static function date($date)
	{
		if (empty($date) || is_null($date) || $date == '0000-00-00 00:00:00')
		{
			return $date;
		}

		try {
			$dateOffset = new DateTime(
				date($date), 
				new DateTimeZone(JFactory::getApplication()->getCfg('offset', 'UTC'))
			);

			return $dateOffset->format('c');

		} catch (Exception $e) {
			return $date;
		}
	}

	/**
	 *  Determine if the user is viewing the front page
	 *
	 *  @return  boolean
	 */
	public static function isFrontPage()
	{
		$menu = JFactory::getApplication()->getMenu();
		$lang = JFactory::getLanguage()->getTag();
		return ($menu->getActive() == $menu->getDefault($lang));
	}

    /**
     *  Logs messages to log file
     *
     *  @param   object  $type  The log type
     *
     *  @return  void
     */
    public static function log($msg)
    {
		self::$log[] = $msg;
    }

    /**
     *  Renders Backend's Sidebar
     *
     *  @return  string  The HTML output
     */
    public static function renderSideBar()
    {
		$data = array(
			'view'  => JFactory::getApplication()->input->get('view', 'gsd'),
			'items' => array(
				array(
					'label' => 'NR_DASHBOARD',
					'url'   => 'index.php?option=com_gsd',
					'icon'  => 'dashboard',
					'view'  => 'gsd'
				),
				array(
				 	'label' => 'GSD_ITEMS',
					'url'   => 'index.php?option=com_gsd&view=items',
					'icon'  => 'list',
					'view'  => 'items,item'
				),
				array(
				 	'label' => 'GSD_CONFIG',
					'url'   => 'index.php?option=com_gsd&view=config&layout=edit',
					'icon'  => 'options',
					'view'  => 'config'
				),
				array(
				 	'label'  => 'NR_DOCUMENTATION',
					'url'    => 'https://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/',
					'icon'   => 'file-2',
					'target' => 'blank'
				),
				array(
				 	'label'  => 'NR_SUPPORT',
					'url'    => 'http://www.tassos.gr/contact',
					'icon'   => 'help',
					'target' => 'blank'
				)
			)
		);

        // Render layout
        $layout = new JLayoutFile('sidebar', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
        return $layout->render($data);
    }

    /**
     *  Get website name
     *
     *  @return  string  Site URL
     */
    public static function getSiteName()
    {
        return self::getParams()->get("sitename_name", JFactory::getConfig()->get('sitename'));
    }
    
    /**
     *  Returns the Site Logo URL
     *
     *  @return  string
     */
    public static function getSiteLogo()
    {
        if (!$logo = self::getParams()->get("logo_file", null))
        {
            return;
        }

        return JURI::root() . $logo;
    }

	/**
	 *  Get website URL
	 *
	 *  @return  string  Site URL
	 */
	public static function getSiteURL()
	{
		return self::getParams()->get("sitename_url", JURI::root());
	}

    /**
     *  Get Plugin Parameters
     *
     *  @return  JRegistry
     */
    public static function getParams()
    {
    	if (self::$params)
		{
			return self::$params;
		}

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');
		
		$table = JTable::getInstance('Config', 'GSDTable');
		$table->load('config');

		return (self::$params = new Registry($table->params));
    }

    /**
     *  Returns permissions
     *
     *  @return  object
     */
    public static function getActions()
    {
        $user = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_gsd';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     *  Get list with all available plugins
     *
     *  @return  array
     */
    public static function getPlugins()
    {
        return self::event()->trigger('onGSDGetType');
    }

    /**
     *  Get the 1st found plugin's alias
     *
     *  @return  string  The plugin's alias
     */
    public static function getDefaultPlugin()
    {
    	$plugins = self::getPlugins();
        return $plugins[0]["alias"];
    }

    /**
     *  Get thing's title
     *
     *  @param   integer  $id         The thing's ID
     *  @param   string   $extension  The thing's extension alias
     *
     *  @return  string              
     */
    public static function getThingTitle($id, $extension)
    {
        return implode(" ", self::event()->trigger('onGSDGetItemTitle', array($id, $extension)));
    }

    /**
     *  Loads all GSD plugins and returns a dispatcher instance
     *
     *  @return  JEventDispatcher
     */
    public static function event()
    {
        JPluginHelper::importPlugin('gsd');
        return JEventDispatcher::getInstance();
    }

	/**
     *  Returns active component alias
     *
     *  @return  mixed String on success, false on failure
     */
    public static function getComponentAlias()
    {
        if (!$option = JFactory::getApplication()->input->get("option"))
        {
            return;
        }

        $optionParts = explode("_", $option);
        
        return isset($optionParts[1]) ? $optionParts[1] : false;
    }

    /**
     *  Formats Price value according to http://schema.org/price
     *
     *  @param   string  $price  The price
     *
     *  @return  string          The new formated price
     */
    public static function formatPrice($price)
    {
    	return number_format($price, 2, '.', '');
    }

    /**
     *  Renders the Upgrade to Pro field
     *
     *  @return  string
     */
    public static function getProField($label = 'NR_UPGRADE_TO_PRO_TO_UNLOCK')
    {
        static $proField;

        if ($proField)
        {
        	return $proField;
        }

        include_once JPATH_PLUGINS . '/system/nrframework/fields/pro.php';

        $field   = new JFormFieldNR_PRO;
        $element = new SimpleXMLElement('
            <field name="pro" type="nr_pro"
                url="http://www.tassos.gr/joomla-extensions/google-structured-data-markup"
                link="' . $label . '"
            />');

        $field->setup($element, null);

        return ($proField = $field->__get('input'));
    }

    /**
     *  Checks whether the plugin is a Pro version
     *
     *  @return  boolean  
     */
    public static function isPro()
    {
    	return NRFramework\Functions::extensionHasProInstalled('plg_system_gsd');
    }
}

?>