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
use NRFramework\Cache;

class Users extends Assignment 
{
    /**
     *  md5 hash used for caching the groups map
     *
     *  @var [type]
     */
    protected $_groupsHash;

    /**
     *  Constructor
     *
     *  @param object $options
     *  @param object $request
     *  @param object $date
     */
    public function __construct($options, $request = null, $date = null)
    {
        parent::__construct($options, $request = null, $date = null);

        $_groupsHash = md5('NRFramework\Assignments\User_groupsHash');
    }
    
	/**
	 *  Pass Check User Group Levels
	 *
	 *  @return  bool
	 */
	public function passGroupLevels()
	{
        $groups = $this->getGroups();

        // replace group names with ids in selection
        foreach ($this->selection as $key => $id)
        {
            if (!is_numeric($id))
            {
                $this->selection[$key] = array_search(strtolower($id), $groups);
            }
        }

		$usergroups = !empty($this->user->groups) ? array_values($this->user->groups) : $this->user->getAuthorisedGroups();
    	return $this->passSimple($usergroups, $this->selection); 
	}

	/**
	 *  Pass Check User's Time on Site
	 *
	 *  @return  bool
	 */
	public function passTimeOnSite()
	{
		$pass = false;

		$sessionStartTime = strtotime($this->SessionStartTime());

		if (!$sessionStartTime)
		{
			return $pass;
		}

		$dateTimeNow = strtotime(\NRFramework\Functions::dateTimeNow());
		$diffInSeconds = $dateTimeNow - $sessionStartTime;

		if (intval($this->selection) <= $diffInSeconds)
		{
			$pass = true;
		}

		return $pass;
	}

	/**
	 * Check the number of pageviews
	 *
	 * @return bool
	 */
	public function passPageviews()
	{
		if (is_null($this->params->views) || !is_numeric($this->params->views))
		{
			return;
		}

		$pageviews = intval($this->params->views);
		$visits    = \JFactory::getSession()->get('session.counter', 0);
		$pass      = false;

		switch ($this->selection)
		{
			case 'fewer':
				$pass = $visits < $pageviews;
				break;
			case 'greater':
				$pass = $visits > $pageviews;
				break;
			default: // 'exactly'
				$pass = $visits === $pageviews;
				break;
		}

		return $pass;
	}

	/**
	 * Check User ID
	 *
	 * @return bool
	 */
	public function passIDs()
	{
		$this->selection = is_array($this->selection) ? $this->selection : explode(',', $this->selection);

		// prepare an array(of ints) from the supplied IDs(string)		
		$ids = array_map('intval', array_map('trim', $this->selection));

		if (in_array($this->user->id, $ids))
		{
			return true;
		}

		return false;
	}

    private static function SessionStartTime()
    {
        $session = \JFactory::getSession();
        
        $var = 'starttime';
        $sessionStartTime = $session->get($var);

        if (!$sessionStartTime)
        {
            $date = \NRFramework\Functions::dateTimeNow();
            $session->set($var, $date);
        }

        return $session->get($var);
    }

    /**
     *  Returns User Groups map (ID => Name)
     *
     *  @return array
     */
    protected function getGroups()
    {
        if (Cache::has($this->_groupsHash))
        {
            return Cache::get($this->_groupsHash);
        }

        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('id, title')
            ->from('#__usergroups');
        $db->setQuery($query);
        
        $res = $db->loadObjectList();
        $groups = [];
        foreach ($res as $r)
        {
            $groups[$r->id] = strtolower($r->title);
        }
        Cache::set($this->_groupsHash, $groups);

        return $groups;
    }
}
