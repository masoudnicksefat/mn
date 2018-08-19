<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 *  Novarain Framework Assignments Helper Class
 */
class Assignments
{
    /**
	 *  Assignment Type Aliases
	 *
	 *  @var  array
	 */
	public $typeAliases = array(
		'device|devices'                     => 'Devices',
		'urls|url'                           => 'URLs',
		'os'			                     => 'OS',
		'browsers|browser'		             => 'Browsers',
		'referrer'                           => 'URLs.Referrer',
		'lang|language|languages'            => 'Languages',
		'php'                                => 'PHP',
		'timeonsite'                         => 'Users.TimeOnSite',
		'usergroups|usergroup|user_groups'   => 'Users.GroupLevels',
		'pageviews|user_pageviews'           => 'Users.Pageviews',
		'user_id|userid'		             => 'Users.IDs',
		'menu'                               => 'Menu',
        'datetime|daterange|date'            => 'DateTime.Date',
        'days|day'                           => 'DateTime.Days',
        'months|month'                       => 'DateTime.Months',
		'timerange|time'                     => 'DateTime.TimeRange',
        'acymailing'                         => 'AcyMailing',
        'akeebasubs'                         => 'AkeebaSubs',
        'contentcats|categories|category'    => 'Content.Categories',
        'contentarticles|articles|article'   => 'Content.Articles',
        'components|component'	             => 'Components',
        'convertforms'	                     => 'ConvertForms',
        'geo_country|country|countries'	     => 'GeoIP.Countries',
        'geo_continent|continent|continents' => 'GeoIP.Continents',
        'cookiename|cookie'                  => 'Cookies.Name',
        'ip_addresses|iprange|ip'            => 'IP.Range',
        'k2_items'                           => 'K2Item',
        'k2_cats'                            => 'K2Category',
        'k2_tags'                            => 'K2Tag',
        'k2_pagetypes'                       => 'K2Pagetype'
    );
    
    /**
	 *  Check all Assignments
	 *
	 *  @param   array|object   $assignments_info   Array containing assignment info
	 *  @param   string         $match_method       The matching method (and|or)
	 *
	 *  @return  bool           True if check passes
	 */
	function passAll($assignments_info, $match_method = 'and')
	{
        if (!$assignments_info)
        {
            return true;
        }

        // convert $assignments_info parameter from object (used by existing extensions)
        // to array
        if (is_object($assignments_info))
        {
            $assignments_info = $this->prepareAssignmentsInfo($assignments_info);
        }

        // filter-out invalid assignments and prepare assignment data (new method - added for Restrict Content)
        $assignments = $this->prepareAssignments($assignments_info);

        // initialize $pass based on the matching method
        $pass = (bool) ($match_method == 'and');

        foreach ($assignments as $a)
        {
            // Return false if any of the assignments doesnt exist
            if (is_null($a))
            {
                return false;
            }

            // Break if not passed and matching method is AND
			// Or if passed and matching method is OR
			if (
				(!$pass && $match_method == 'and')
				|| ($pass && $match_method == 'or')
			)
			{
				break;
            }
            
            $assignment = new $a->class($a->options);
            $pass       = $assignment->{$a->method}();
            $pass       = $this->passStateCheck($pass, $a->options->assignment_state);
        }

        return $pass;
    }

    /**
     *  Checks if an assignment exists
     *
     *  @param  string $assignment Assignment class name or alias
     *  @return bool
     */
    public function exists($assignment)
    {
        if (!$assignment)
        {
            return false;
        }
        $assignment = strtolower($assignment);

        // search by Assignment name
        if (array_search($assignment, $this->typeAliases) !== false)
        {
            return true;
        }

        // search assignment aliases
        foreach (array_keys($this->typeAliases) as $key)
        {
            if (strpos($key, $assignment) !== false)
            {
                return true;
            }
        }
        return false;
    }

    /**
     *  Returns the classname for a given assignment alias
     *
     *  @param  string       $alias
     *  @return string|void
     */
    public function aliasToClassname($alias)
    {
        $alias = strtolower($alias);
        foreach ($this->typeAliases as $aliases => $type)
        {
            if (strtolower($type) == $alias)
            {
                return $type;
            }

            $aliases = explode('|', strtolower($aliases));
            if (in_array($alias, $aliases))
            {
                return $type;                
            }   
        }

        return null;
    }

    /**
    *  Assignment pass check based on the assignment state
    *
    *  @param   boolean  $pass        
    *  @param   string   $assignment_state  The assignment state
    *
    *  @return  boolean
    */
    private function passStateCheck($pass = true, $assignment_state = null)
    {
        $assignment_state = $assignment_state ?: $this->assignment;
        return $pass ? ($assignment_state == 'include') : ($assignment_state == 'exclude');
    }

    /**
     * Checks and prepares the given array of assignment information
     * 
     * @return  array of objects
     */
    protected function prepareAssignments($assignments_info)
    {
        $assignments = array();
        foreach ($assignments_info as $a)
        {
            if (!is_object($a) ||!isset($a->alias) || !isset($a->selection) ||
                !isset($a->params) || !isset($a->assignment_state))
            {
                continue;
            }

            $assignment = new \stdClass();
            
            // check if the assignment type exists
            if (!$this->exists($a->alias) || !$this->setTypeParams($assignment, $this->aliasToClassname($a->alias)))
            {
                $assignment = null;
            }
            else
            {
                $assignment->options = (object) array(
                    'selection'         => $a->selection,
                    'params'            => $a->params,
                    'assignment_state'  => $this->getAssignmentState($a->assignment_state)
                );
            }

            $assignments[] = $assignment;
        }

        return $assignments;
    }

    /**
     * Converts an object of assignment information to an array of objects
     * Used by existing extensions
     * @return array of objects
     */
    protected function prepareAssignmentsInfo($assignments_info)
    {
        if (!isset($assignments_info->params))
        {
            return [];
        }

        $params = json_decode($assignments_info->params);

        if (!is_object($params))
		{
			return [];
        }

        $assignments_info = [];
        
        foreach ($this->typeAliases as $aliases => $type)
        {
            $aliases = explode('|', $aliases);

            foreach ($aliases as $alias)
            {
                if (!isset($params->{'assign_' . $alias}) || !$params->{'assign_' . $alias})
                {
                    continue;
                }

                // Discover assignment params
                $assignment_params = new \stdClass();
                foreach ($params as $key => $value)
                {
                    if (strpos($key, "assign_" . $alias . "_param") !== false)
                    {
                        $key = str_replace("assign_" . $alias . "_param_", "", $key);
                        $assignment_params->$key = $value;
                    }
                }

                $assignments_info[] = (object) array(
                    'alias'              => $alias,
                    'assignment_state'  => $this->getAssignmentState($params->{'assign_' . $alias}),
                    'selection'         => isset($params->{'assign_' . $alias . '_list'}) ? $params->{'assign_' . $alias . '_list'} : array(),
                    'params'            => $assignment_params
                );
            }
        }

        return $assignments_info;
    }

    /**
	 *  Returns assignment's state by ID
	 *  1: Include
	 *  2: Exclude
	 *  3, -1: None
	 *
	 *  @param   integer  $state_id     Assignment's state ID
	 *
	 *  @return  string                 Assignment's state name
	 */
	private function getAssignmentState($state_id)
	{
		switch ($state_id)
		{
			case 1:
			case 'include':
				$assignment_state = 'include';
				break;
			case 2:
			case 'exclude':
				$assignment_state = 'exclude';
				break;
			case 3:
			case -1:
			case 'none':
				$assignment_state = 'none';
				break;
			default:
				$assignment_state = 'all';
				break;
		}

		return $assignment_state;
    }
    
    /**
	 *  Sets proper assignment class and method name
	 *
	 *  @param   object  &$assignment  The assignment object
	 *  @param   string  $type         The assignment type
	 *
	 *  @return  void
	 */
	public function setTypeParams(&$assignment, $type = '')
	{
		if (strpos($type, '.') === false)
		{
			$class = $type;
			$method  = $type;
        }
        else
        {
            $type = explode('.', $type, 2);
            $class = $type['0'];
            $method  = $type['1'];
        }		

        $class      = __NAMESPACE__ . '\\Assignments\\' . $class;
        $method     = 'pass' . $method;
        if (!class_exists($class) && !method_exists($class, $method))
        {
            return false;
        }
        
        $assignment->class = $class;
        $assignment->method = $method;

        return true;
	}
}

?>