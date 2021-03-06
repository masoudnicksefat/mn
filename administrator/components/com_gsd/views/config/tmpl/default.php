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
JHtml::_('behavior.modal');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');

?>

<div class="nr-app nr-app-config">
    <div class="nr-row">
        <?php echo $this->sidebar; ?>
        <div class="nr-main-container">
            <div class="nr-main-header">
                <h2><?php echo JText::_('GSD_CONFIG'); ?></h2>
                <p><?php echo JText::_('GSD_CONFIG_DESC'); ?></p>
            </div>
            <div class="nr-main-content">
        		<form action="<?php echo JRoute::_('index.php?option=com_gsd&view=config'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    		      <div class="form-horizontal">
                    	<?php 
                            echo JHtml::_('bootstrap.startTabSet', 'tab', array('active' => 'globaldata'));

                            foreach ($this->form->getFieldSets() as $key => $fieldset)
                            {
                                echo JHtml::_('bootstrap.addTab', 'tab', $fieldset->name, JText::_($fieldset->label));
                                echo $this->form->renderFieldSet($fieldset->name);
                                echo JHtml::_('bootstrap.endTab');
                            }

                            echo JHtml::_('bootstrap.endTabSet');
                        ?>
        		    </div>

        		    <?php echo JHtml::_('form.token'); ?>
        		    <input type="hidden" name="task" value="" />
        		    <input type="hidden" name="name" value="config" />
        		</form>
            </div>
        </div>
    </div>
</div>