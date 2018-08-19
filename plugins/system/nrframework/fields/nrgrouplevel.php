<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use \NRFramework\HTML;

require_once dirname(__DIR__) . '/helpers/field.php';

/**
 *  GroupLevel Field
 */
class JFormFieldNRGroupLevel extends NRFormField
{
	/**
	 * Output the HTML for the field
	 * Example of usage: <field name="field_name" type="nrgrouplevel" label="NR_SELECTION" show_all="0" size="300" use_names="0"/>
	 * 
	 * @return string 	The HTML for the groupfield
	 */
	protected function getInput()
	{
		$size      = $this->get('size', 300);
		$show_all  = $this->get('show_all');
		$use_names = $this->get('use_names');
		$options   = $this->getUserGroups($use_names);

		if ($show_all)
		{
			$option          = new stdClass;
			$option->value   = -1;
			$option->text    = '- ' . JText::_('JALL') . ' -';
			$option->disable = '';
			
			array_unshift($options, $option);
		}

		return HTML::treeselect($options, $this->name, $this->value, $this->id, $size);
	}

	/**
	 * A helper to get the list of user groups.
	 * Logic from administrator\components\com_config\model\field\filters.php@getUserGroups
	 * 
	 * @param   boolen 	$useNames 	Whether to use the names or the IDs
	 * 
	 * @return	object
	 */
	protected function getUserGroups($useNames = false)
	{
		$value = $useNames ? 'a.title' : 'a.id';

		// Get a database object.
		$db = $this->db;

		// Get the user groups from the database.
		$query = $db->getQuery(true)
			->select($value . ' AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups AS a')
			->join('LEFT', '#__usergroups AS b on a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft')
			->order('a.lft ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}