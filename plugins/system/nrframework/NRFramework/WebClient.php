<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined( '_JEXEC' ) or die( 'Restricted access' );

class WebClient
{
	/**
	 *  Joomla Application Client
	 *
	 *  @var  object
	 */
	public static $client;

	/**
	 *  Get visitor's Device Type
	 *
	 *  @return  string    The client's device type. Can be: tablet, mobile, desktop
	 */
	public static function getDeviceType()
	{
        if (!class_exists('Mobile_Detect'))
        {
        	\JLoader::register('Mobile_Detect', JPATH_PLUGINS . '/system/nrframework/helpers/vendors/Mobile_Detect.php');
        }

        $detect = new \Mobile_Detect;

        return ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');
	}

	/**
	 *  Get visitor's Operating System
	 *
	 *  @return  string     Possible values: any of JApplicationWebClient's OS constants (except 'iphone' and 'ipad'), 
     *                                       'ios', 'chromeos'
	 */
	public static function getOS()
	{
        // detect iOS and CromeOS (not handled by JApplicationWebClient)
        $ua = self::getClient()->userAgent;

        $ios_regex = '/iPhone|iPad|iPod/i';
        if (preg_match($ios_regex, $ua))
        {
            return 'ios';
        }

        $chromeos_regex = '/CrOS/i';
        if (preg_match($chromeos_regex, $ua))
        {
            return 'chromeos';
        }

        // use JApplicationWebClient for OS detection
		$platformInt = self::getClient()->platform;
		$constants   = self::getClientConstants();
		
		if (isset($constants[$platformInt]))
		{
			return strtolower($constants[$platformInt]);
		}
	}

	/**
	 *  Get visitor's Browser name / version
	 *
	 *  @return  array
	 */
	public static function getBrowser()
	{
		$client     = self::getClient();
		$browserInt = $client->browser;
		$constants  = self::getClientConstants();

		if (isset($constants[$browserInt]))
		{
			return array(
				'name'    => strtolower($constants[$browserInt]),
				'version' => $client->browserVersion
			);
		}
	}

	/**
	 *  Get the constants from JApplicationWebClient as an array using the Reflection API
	 *
	 *  @return  array
	 */
	private static function getClientConstants()
	{
		$r = new \ReflectionClass('JApplicationWebClient');
		$constantsArray = $r->getConstants();

		// flip the associative array
		return array_flip($constantsArray);
	}

	/**
	 *  Get the Application Client helper
	 *
	 *  see https://api.joomla.org/cms-3/classes/Joomla.Application.Web.WebClient.html
	 *
	 *  @return  object
	 */
	public static function getClient()
	{
		if (is_object(self::$client))
		{
			return self::$client;
		}

		return (self::$client = \JFactory::getApplication()->client);
	}
}