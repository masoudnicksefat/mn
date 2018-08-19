<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Initialize Novarain Library
require_once __DIR__ . '/autoload.php';

class plgSystemNRFramework extends JPlugin
{
	/**
	 *  Auto load plugin language 
	 *
	 *  @var  boolean
	 */
	protected $autoloadLanguage = true;
	
	/**
	 *  The Joomla Application object
	 *
	 *  @var  object
	 */
	protected $app;

 	/**
     *  Plugin constructor
     *
     *  @param  mixed   &$subject
     *  @param  array   $config
     */
    public function __construct(&$subject, $config = array())
    {
        // Declare extension logger
        JLog::addLogger(
            array('text_file' => 'plg_system_nrframework.php'),
            JLog::ALL, 
            array('nrframework')
        );

        // execute parent constructor
        parent::__construct($subject, $config);
    }

    /**
     *  Update UpdateSites after the user has entered a Download Key
     *
     *  @param   string  $context  The component context
     *  @param   string  $table    
     *  @param   boolean $isNew    
     *
     *  @return  void
     */
	public function onExtensionAfterSave($context, $table, $isNew)
	{
		// Run only on Novarain Framework edit form
		if (
			$this->app->isSite()
			|| $context != 'com_plugins.plugin'
			|| $table->element != 'nrframework'
			|| !isset($table->params)
		)
		{
			return;
		}

		// Set Download Key & fix Update Sites
		$upds = new NRFramework\Updatesites();
		$upds->update();
	}

	/**
	 *  Handling of PRO for extensions
	 *  Throws a notice message if the Download Key is missing before downloading the package
	 *
	 *  @param   string  &$url      Update Site URL
	 *  @param   array   &$headers  
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$isOurExtension = JString::strrpos($url, "tassos.gr/update");

		if (!$isOurExtension)
		{
			return;
		}

		preg_match("/dlid=.+/", $url, $hasDownloadKey);

		$isFree         = JString::strrpos($url, "free");
		$hasDownloadKey = (count($hasDownloadKey) > 0) ? true : false;

		if ($hasDownloadKey || $isFree)
		{
			return;
		}

		$this->app->enqueueMessage('To be able to update the Pro version of this extension via the Joomla updater, you will need enter your Download Key in the settings of the <a href="'.JURI::base().'index.php?option=com_plugins&view=plugins&filter_search=novarain">Novarain Framework System Plugin</a>');

		return false;
	}
}
