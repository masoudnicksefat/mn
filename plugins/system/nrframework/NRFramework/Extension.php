<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined( '_JEXEC' ) or die( 'Restricted access' );

class Extension
{
	public static function pluginIsEnabled($element, $folder = 'system') 
	{
		if (!$extension = self::get($element, $type = 'plugin', $folder))
		{
			return false;
		}

		return $extension['enabled'];
	}

	public static function componentIsEnabled($element) 
	{
		$element = 'com_' . str_replace('com_', '', $element);

		if (!$extension = self::get($element))
		{
			return false;
		}

		return $extension['enabled'];
	}

	public static function getID($element, $type = 'component', $folder = null)
	{
		if (!$extension = self::get($element, $type, $folder))
		{
			return false;
		}	

		return $extension['id'];
	}

    /**
     *  Returns extension ID
     *
     *  @param   string  $element  Element name
     *
     *  @return  integer
     */
    public static function get($element, $type = 'component', $folder = null)
    {
        $db = \JFactory::getDBO();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element))
            ->where($db->quoteName('type') . ' = ' . $db->quote($type));

        if ($folder)
        {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        }

        $db->setQuery($query);

        return $db->loadAssoc();
    }

}