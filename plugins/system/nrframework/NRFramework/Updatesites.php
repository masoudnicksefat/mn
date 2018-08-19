<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined( '_JEXEC' ) or die( 'Restricted access' );

class Updatesites
{
	/**
	 *  Joomla Database Class
	 *
	 *  @var  object
	 */
	private $db;

	/**
	 *  Download Key
	 *
	 *  @var  string
	 */
	private $key;

	/**
	 *  Consturction method
	 *
	 *  @param  string  $key  Download Key
	 */
	function __construct($key = null)
	{
		$this->db = \JFactory::getDBO();
		$this->key = ($key) ? $key : $this->getDownloadKey();
   	}

   	/**
   	 *  Main method
   	 */
   	public function update()
   	{
   		$this->removeOld();
   		$this->removeDuplicate();
   		$this->updateDownloadKey();
   	}

   	/**
   	 *  Removes old entries
   	 */
	private function removeOld()
	{
		$db = $this->db;
		$query = $db->getQuery(true)
			->select('update_site_id')
			->from('#__update_sites')
			->where($db->qn('location') . ' LIKE ' . $db->q('http://www.tassos.gr%'));

		$db->setQuery($query);
		$id = $db->loadColumn();

		$this->remove($id);
	}

	/**
	 *  Removes duplicate entries 
	 *  Issue: https://github.com/joomla/joomla-cms/issues/8512
	 */
	private function removeDuplicate()
	{
		// Fetch extension with multiple entries
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('u.extension_id'))
			->from($this->db->quoteName('#__update_sites_extensions', 'u'))
			->join('INNER', $this->db->quoteName('#__update_sites', 'us') . ' ON (' . $this->db->quoteName('u.update_site_id') . ' = ' . $this->db->quoteName('us.update_site_id') . ')')
			->where($this->db->quoteName('us.location') . 'LIKE ' . $this->db->quote('%tassos.gr%'))
			->group('u.extension_id HAVING COUNT(*) > 1');
		$this->db->setQuery($query);
		
		$ids = $this->db->loadColumn();

		if (!$ids || !is_array($ids))
		{
			return;
		}

		// Fetch update site ids
		$duplicateEntries = array();

		foreach ($ids as $key => $value)
		{
			$query->clear()
				->select($this->db->quoteName('u.update_site_id'))
				->from($this->db->quoteName('#__update_sites', 'u'))
				->join('INNER', $this->db->quoteName('#__update_sites_extensions', 'su') . ' ON (' . $this->db->quoteName('u.update_site_id') . ' = ' . $this->db->quoteName('su.update_site_id') . ')')
				->where($this->db->qn('su.extension_id') . ' = ' . $value)
				->order($this->db->quoteName('su.update_site_id') . 'DESC');
			$this->db->setQuery($query, 1, 10000);
			$ids = $this->db->loadColumn();

			$duplicateEntries = array_merge($ids, $duplicateEntries);
		}

		// Delete duplicate entries
		if (!$duplicateEntries)
		{
			return;
		}

		$this->remove($duplicateEntries);
	}

	/**
	 *  Removes entries from the update sites tables
	 *
	 *  @param   array  $ids  Update sites ids
	 */
	private function remove($ids)
	{
		if (!is_array($ids) || count($ids) == 0)
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->delete('#__update_sites')
			->where($this->db->qn('update_site_id') . ' IN ('.implode(",", $ids).')');
		$this->db->setQuery($query);
		$this->db->execute();

		$query->clear()
			->delete('#__update_sites_extensions')
			->where($this->db->qn('update_site_id') . ' IN ('.implode(",", $ids).')');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 *  Reads the Download Key saved in the Novarain Framework system plugin parameters
	 *
	 *  @return  string  The Download Key
	 */
	public function getDownloadKey()
	{
		$query = $this->db->getQuery(true)
			->select('e.params')
			->from('#__extensions as e')
			->where('e.element = ' . $this->db->quote('nrframework'));
		$this->db->setQuery($query);
		$params = $this->db->loadResult();

		if (!$params)
		{
			return false;
		}

		$params = json_decode($params);

		if (!isset($params->key))
		{
			return false;
		}

		return $params->key;
	}

	/**
	 *  Adds the user's Download Key as an extra query parameter to all entries
	 *
	 *  @param   string  $key  Download Key
	 */
	private function updateDownloadKey()
	{
		$db = $this->db;
		$query = $db->getQuery(true)
			->update('#__update_sites')
			->set($db->qn('extra_query') . ' = ' . $db->q('dlid=' . trim($this->key)))
			->set($db->qn('enabled') . ' = 1')
			->where($db->qn('location') . ' LIKE ' . $db->q('%tassos.gr%'));

		$db->setQuery($query);
		$db->execute();
	}
}