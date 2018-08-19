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

?>

<div class="nr-app">
    <div class="nr-row">
        <?php echo $this->sidebar; ?>
        <div class="nr-main-container">
            <div class="nr-main-header">
                <h2><?php echo JText::_('NR_DASHBOARD'); ?></h2>
                <p><?php echo JText::_('GSD_DASHBOARD_DESC'); ?></p>
            </div>
            <div class="nr-main-content">
                <div class="tile is-ancestor">
                    <div class="tile is-vertical">
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo JURI::base() ?>index.php?option=com_gsd&view=items">
                                                <?php echo JText::_('GSD_TOTAL_ITEMS'); ?>
                                            </a>
                                            <div><?php echo JText::_('GSD_TOTAL_ACTIVE_ITEMS'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right">
                                            <span class="nr-number"><?php echo $this->stats['itemsCount']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo JURI::base() ?>index.php?option=com_gsd&view=config&layout=edit#globaldata">
                                                <?php echo JText::_('GSD_GLOBAL_DATA'); ?>
                                            </a>
                                            <div><?php echo JText::_('GSD_GLOBAL_DATA_SUBHEADING'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right">
                                            <span class="nr-number">
                                                <?php echo $this->stats['siteDataEnabled'] ?>/<?php echo count($this->stats['siteData']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child">
                                    <div class="nr-box nr-box-hr">
                                        <div class="nr-box-title">
                                            <a href="<?php echo JURI::base() ?>index.php?option=com_gsd&view=config&layout=edit#integrations">
                                                <?php echo JText::_('GSD_INTEGRATIONS'); ?>
                                            </a>
                                            <div><?php echo JText::_('GSD_TOTAL_ACTIVE_INTEGRATIONS'); ?></div>
                                        </div>
                                        <div class="nr-box-content text-right">
                                            <span class="nr-number">
                                                 <?php echo $this->stats['integrationsEnabled'] ?>/<?php echo count($this->stats['integrations']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child nr-box">
                                    <?php echo $this->loadTemplate('contenttypes'); ?>
                                </div>
                            </div>
                            <div class="tile is-parent">
                                <div class="tile is-child nr-box">
                                    <?php echo $this->loadTemplate('sitedata'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tile">
                            <div class="tile is-parent">
                                <div class="tile is-child ">
                                    <?php echo $this->loadTemplate('tester'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tile is-3 is-parent">
                        <div class="tile is-child nr-box">
                            <?php echo $this->loadTemplate('right'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>