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
JHTML::_('behavior.modal'); 
extract($displayData);

$isPro = GSDHelper::isPro();

?>

<div class="nr-app-addons" data-base="<?php echo JURI::base() ?>">
    <table class="table nrTable">
    	<?php foreach ($items as $key => $item) { 
    		$docsURL    = 'http://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/' . $item['docalias'];
    		$optionsURL = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $item['id'];
    	?>
        <tr data-id="<?php echo $item['id']; ?>">
            <td style="width:60px;">
                <img alt="<?php echo $item["label"]; ?>" src="//static.tassos.gr/images/integrations/gsd/<?php echo $item["name"]; ?>.png"/>
            </td>
            <td>
                <div class="addonTitle"><?php echo JText::_($item["label"]); ?></div>
                <div class="addonDesc"><?php echo JText::_($item["description"]); ?></div>
            </td>
            <td class="addonButtons">
                <?php if ($item['comingsoon']) { ?>
                        <span class="icon-star-empty"></span>
                        <?php echo JText::_('NR_COMING_SOON'); ?>
                    </a>
                <?php } ?>
                
                <?php if (!$item['comingsoon']) { ?>
                    <?php if ($item['id']) { ?>
        				<a class="btn pluginState" href="#" title="<?php echo JText::_('GSD_INTEGRATION_TOGGLE') ?>">
        					<span class="icon-<?php echo $item['isEnabled'] ? "publish" : "unpublish" ?>"></span>
        				</a>
              			<a class="btn modal" rel="{handler: 'iframe', size: {x: 1000, y: 600}}" href="<?php echo $optionsURL; ?>" target="_blank" title="<?php echo JText::_("JOPTIONS") ?>">
                        	<span class="icon-options "></span>
                        </a>
                    <?php } ?>
                    
                    <a class="btn" href="<?php echo $docsURL; ?>" target="_blank" title="<?php echo JText::_("NR_DOCUMENTATION") ?>">
                        <span class="icon-help"></span>
                    </a>
                    <?php if (!$isPro && isset($item['image'])) { ?>
                        <a class="btn modal" href="<?php echo $item['image']; ?>" title="?php echo JText::_('NR_SAMPLE') ?>">
                            <span class="icon-image"></span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </td>
        </tr>
    	<?php } ?>
		<tr>
			<td>
                <a target="_blank" target="_blank" href="https://www.tassos.gr/contact">
                    <img alt="<?php echo $item["description"]; ?>" src="//static.tassos.gr/images/integrations/addon.png"/>
                </a>
            </td>
            <td>
                <div class="addonTitle"><?php echo JText::_("GSD_INTEGRATIONS_MISSING") ?></div>
                <?php echo JText::_("GSD_INTEGRATIONS_MISSING_DESC") ?>
            </div>
            <td class="addonButtons" colspan="2">
                <a class="btn" target="_blank" href="https://www.tassos.gr/contact">
                    <span class="icon-mail"></span>
                	<?php echo JText::_("NR_CONTACT_US")?>
                </a>
            </td>
		</tr>
	</table>
</div>