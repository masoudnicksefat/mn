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

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

/**
 *  Google Structured Data component installer
 */
class Com_GSDInstallerScript extends Com_GSDInstallerScriptHelper
{
	public $name = 'GSD';
	public $alias = 'gsd';
	public $extension_type = 'component';

	/**
	 *  Runs after current extension installation
	 *
	 *  @return  void
	 */
	public function onAfterInstall()
	{
		// Only on update
		if ($this->install_type == 'install')
		{
			return true;
		}

		$this->loadFramework();
		$version = \NRFramework\Functions::getExtensionVersion('plg_system_gsd');

		// Make sure we support the current version
		if ($version && version_compare($version, '3.0', '<='))
		{
			require_once $this->getMainFolder() . '/helpers/migrator.php';
			$migrator = new GSDMigrator();
			$migrator->start();
		}

		// Custom Code new requirements
		$this->migrateCustomCode();
	}

    /**
     *	Since v3.1.3 the saving policy of Custom Code has been changed which is now being saved independently
     *	This method helps up, transition to new requirements. 
     *
     *  @return  void
     */
	private function migrateCustomCode()
    {
        $items = $this->getItems();

        foreach ($items as $key => $item)
        {   
         	if ($item->contenttype == 'custom_code')
            {
                continue;
            }

            if (!isset($item->customcode) || empty($item->customcode) || is_null($item->customcode))
            {
                continue;
            }

            $data = array(
                'thing'       => $item->thing,
                'plugin'      => $item->plugin,
                'params'      => json_encode(array(
                    'contenttype' => 'custom_code',
                    'customcode'  => $item->customcode
                )),
                'state'       => $item->state,
                'note'        => $item->note,
                'colorgroup'  => $item->colorgroup
            );

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/tables');

            // Save new row
            $model = JModelLegacy::getInstance('Item', 'GSDModel');
            $data  = $model->validate(null, $data);
            $model->save($data);

            // Remove Custom Code from old row
            $table = JTable::getInstance('Item', 'GSDTable');
            $table->load($item->id);

            $p = json_decode($table->params);
            unset($p->customcode);

            $table->params = json_encode($p);
            $table->store();
        }
    }

    private function getItems()
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_gsd/models');

        $model = JModelLegacy::getInstance('Items', 'GSDModel');
        return $model->getItems();
    }
}

 