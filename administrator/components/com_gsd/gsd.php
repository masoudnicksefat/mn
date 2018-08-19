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

// Load Framework
if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Novarain Framework is not installed', 500);
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_gsd'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

// Check required extensions
$app = JFactory::getApplication();

if (!NRFramework\Extension::pluginIsEnabled('gsd'))
{
	$app->enqueueMessage(JText::sprintf('NR_EXTENSION_REQUIRED', JText::_('GSD'), JText::_('PLG_SYSTEM_GSD')), 'error');
}

if (!NRFramework\Extension::pluginIsEnabled('nrframework'))
{
	$app->enqueueMessage(JText::sprintf('NR_EXTENSION_REQUIRED', JText::_('GSD'), JText::_('PLG_SYSTEM_NRFRAMEWORK')), 'error');
}

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php';

JHtml::_('jquery.framework');

NRFramework\Functions::addMedia(array("styles.css", "script.js"), "com_gsd");

GSDHelper::event()->trigger('onGSDGetNames');

// Perform the Request task
$controller = JControllerLegacy::getInstance('GSD');
$controller->execute($app->input->get('task'));
$controller->redirect();
