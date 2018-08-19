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

JHtml::_('bootstrap.popover');
JHtml::_('formbehavior.chosen', 'select');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$showcolors = $this->config->get("colorgroup", true);
$user       = JFactory::getUser();

?>

<div class="nr-app">
    <div class="nr-row">
        <?php echo $this->sidebar; ?>
        <div class="nr-main-container">
        
            <div class="nr-main-header">
                <h2><?php echo JText::_('GSD_ITEMS'); ?></h2>
                <p><?php echo JText::_('GSD_ITEMS_DESC'); ?></p>
            </div>

            <div class="nr-main-content">
                <form action="<?php echo JRoute::_('index.php?option=com_gsd&view=items'); ?>" class="nr-app-form" method="post" name="adminForm" id="adminForm">
                <div class="nr-box">
                    <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
                    <table class="adminlist nrTable table">
                        <thead>
                            <tr>
                                <th class="center" width="2%"><?php echo JHtml::_('grid.checkall'); ?></th>
                                <th width="3%" class="nowrap hidden-phone" align="center">
                                    <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                                </th>
                                <?php if ($showcolors) { ?>
                                    <th width="1%"></th>
                                <?php } ?>
                                <th>
                                    <?php echo JHtml::_('searchtools.sort', 'GSD_ITEM', 'a.thing', $listDirn, $listOrder); ?>
                                </th>
                                <th width="15%">
                                    <?php echo JHtml::_('searchtools.sort', 'GSD_INTEGRATION', 'a.plugin', $listDirn, $listOrder); ?>
                                </th>
                                <th width="15%">
                                    <?php echo JHtml::_('searchtools.sort', 'GSD_CONTENT_TYPE', 'a.snippet', $listDirn, $listOrder); ?>
                                </th>
                                <th width="15%">
                                    <?php echo JHtml::_('searchtools.sort', 'NR_CREATED_DATE', 'a.created', $listDirn, $listOrder); ?>
                                </th>
                                <th width="3%" class="text-center nowrap hidden-phone">
                                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($this->items)) { ?>
                                <?php foreach($this->items as $i => $item): ?>
                                    <?php 
                                        $canChange  = $user->authorise('core.edit.state', 'com_gsd.item.' . $item->id);
                                        $thingTitle = GSDHelper::getThingTitle($item->thing, $item->plugin); 
                                        $trClass    = array(
                                            'row' . $i % 2,
                                            $thingTitle ? '' : 'error'
                                        );
                                    ?>
                                    <tr class="<?php echo implode(" ", $trClass) ?>">
                                        <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                                        <td class="center">
                                            <div class="btn-group">
                                                <?php echo JHtml::_('jgrid.published', $item->state, $i, 'items.', $canChange); ?>
                                                <?php
                                                if ($canChange)
                                                {
                                                    JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'items');
                                                    JHtml::_('actionsdropdown.' . 'duplicate', 'cb' . $i, 'items');
                                                           
                                                    echo JHtml::_('actionsdropdown.render', $this->escape($item->id));
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <?php if ($showcolors) : ?>
                                            <td class="center inlist">
                                                <?php $color = isset($item->colorgroup) ? $item->colorgroup : ""; ?>
                                                <span class="boxColor">
                                                    <span style="background-color: <?php echo $color ?>;"></span>
                                                </span>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="<?php echo JRoute::_('index.php?option=com_gsd&task=item.edit&id='.$item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
                                                <?php echo $thingTitle; ?>
                                            </a>
                                            <span class="small">(id: <?php echo $item->thing; ?>)</span>

                                            <?php if (isset($item->note)) { ?>
                                                <div class="small" style="opacity:.6;"><?php echo $item->note; ?></div>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo JText::_("PLG_GSD_" . strtoupper($item->plugin) . "_ALIAS"); ?></td>
                                        <td><?php echo JText::_('GSD_' . $item->contenttype); ?></td>
                                        <td><?php echo $item->created; ?></td>
                                        <td class="text-center"><?php echo $item->id ?></td>
                                    </tr>
                                <?php endforeach; ?>  
                            <?php } else { ?>
                                <tr>
                                    <td align="center" colspan="8">
                                        <div align="center">
                                            <?php echo JText::_("NR_NO_ITEMS_FOUND"); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>        
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            </div>
            
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>