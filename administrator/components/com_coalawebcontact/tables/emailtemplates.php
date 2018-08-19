<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Component
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class CoalawebcontactTableEmailtemplates extends JTable {
    
    /**
     * Ensure the params and metadata in json encoded in the bind method
     *
     * @var    array
     * @since  3.3
     */
//    protected $_jsonEncode = array('params');

    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$db  A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__cwcontact_emailtemplates', 'id', $db);
    }

    /**
     * Overload the store method for the Coalawebtraffic table.
     *
     * @param	boolean	Toggle whether null values should be updated.
     * @return	boolean	True on success, false on failure.
     * @since	1.6
     */
    public function store($updateNulls = false) {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        
        if ($this->id) {
            // Existing item
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');
        } else {
            // New knownip. A knownip created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!intval($this->created)) {
                $this->created = $date->toSql();
            }
            if (empty($this->created_by)) {
                $this->created_by = $user->get('id');
            }
        }

        // Verify that the alias is unique
        $table = JTable::getInstance('Emailtemplates', 'CoalawebcontactTable');
        if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0)) {
            $this->setError(JText::_('COM_CWCONTACT_ERROR_UNIQUE_ALIAS'));
            return false;
        }
        // Attempt to store the user data.
        return parent::store($updateNulls);
    }
    
    /**
     * Overloaded bind function
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  An optional array or space separated list of properties
     * to ignore while binding.
     *
     * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
     *
     * @see     JTable::bind
     * @since   3.1
     */
    public function bind($array, $ignore = '') {
        
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new Registry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return	boolean	True on success.
     */
    public function check() {

        // Set name
        $this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

        // Set alias
        $this->alias = JApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = JApplication::stringURLSafe($this->title);
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
            $this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
            return false;
        }

        return true;
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0) {
        $k = $this->_tbl_key;

        // Sanitize input.
        ArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
                'UPDATE ' . $this->_db->quoteName($this->_tbl) .
                ' SET ' . $this->_db->quoteName('state') . ' = ' . (int) $state .
                ' WHERE (' . $where . ')' .
                $checkin
        );

        try {
            $this->_db->execute();
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');

        return true;
    }

}
