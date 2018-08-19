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

class GSDModelItems extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     *
     * @see        JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'created', 'a.created',
                'search',
                'ordering', 'a.ordering',
                'plugin', 'a.plugin',
                'contenttype', 'a.contenttype',
                'thing','a.thing'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'a.id', $direction = 'desc')
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts and avoid state conflicts.
        if ($layout = $app->input->get('format'))
        {
            $this->context .= '.' . $layout;
        }

        $thing = $this->getUserStateFromRequest($this->context . '.filter.thing', 'filter_thing');
        $this->setState('filter.thing', $thing);

        $plugin = $this->getUserStateFromRequest($this->context . '.filter.plugin', 'filter_plugin');
        $this->setState('filter.plugin', $plugin);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields from the item table
        $query
            ->select('a.*')
            ->from('#__gsd a');

        // Filter by search
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('a.params LIKE ' . $search);
            }
        }  

        // Filter State
        $state = $this->getState('filter.state');
        if (is_numeric($state))
        {
            $query->where('a.state = ' . (int) $state);
        }
        if (strpos($state, ',') !== false)
        {
            $query->where('a.state IN (' . $state . ')');
        }
        if ($state == '')
        {
            $query->where('a.state IN (0,1,2)');
        }

        // Filter Thing
        if ($thing = $this->getState('filter.thing'))
        {
            $query->where('a.thing = ' . $db->q($thing));
        }

        // Filter Plugin
        if ($plugin = $this->getState('filter.plugin'))
        {
            $query->where('a.plugin = ' . $db->q($plugin));
        }
        
        // Filter Content Type
        if ($contentType = $this->getState('filter.contenttype'))
        {
            $query->where('a.params LIKE ' . $db->q('%"contenttype":"' . $contentType . '"%'));
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'desc');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    /**
     *  [getItems description]
     *
     *  @return  object
     */
    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item)
        {
            $params = json_decode($item->params);
            $items[$key] = (object) array_merge((array) $item, (array) $params);
            unset($items[$key]->params);
        }

        return $items;
    }
}