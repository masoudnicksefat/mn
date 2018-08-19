<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldNR_Geo extends NRFormFieldList
{
	private $list;

	protected function getOptions()
	{
		switch ($this->get('geo'))
		{
			case 'continents':
				$this->list = \NRFramework\Continents::$map;
				$selectLabel = 'NR_SELECT_CONTINENT';
				break;
            default:
				$this->list = \NRFramework\Countries::$map;
				$selectLabel = 'NR_SELECT_COUNTRY';
				break;
		}

		$options = array();

		if ($this->get("showselect", 'true') === 'true')
		{
			$options[] = JHTML::_('select.option', "", "- " . JText::_($selectLabel) . " -");
		}

		foreach ($this->list as $key => $value)
		{
			$options[] = JHTML::_('select.option', $key, $value);
		}

		return array_merge(parent::getOptions(), $options);
	}
}