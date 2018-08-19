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
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$input = JFactory::getApplication()->input;

// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'item.cancel' || document.formvalidator.isValid(document.id('adminForm')))
        {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }

        <?php if ($isModal) { ?>
        if (task !== 'item.apply')
        {
            window.parent.jQuery('#gsdModal').modal('hide');
        }
        <?php } ?>
    }
</script>

<div class="nr-app <?php echo $isModal ? 'nr-isModal' : '' ?>">
    <div class="nr-row">
        <?php 
            if (!$isModal)
            {
                echo $this->sidebar; 
            }
        ?>
        <div class="nr-main-container">
            <?php if (!$isModal) { ?>
            <div class="nr-main-header">
                <h2><?php echo $this->title; ?></h2>
                <p><?php echo JText::_('GSD_ITEM_EDIT_DESC'); ?></p>
            </div>
            <?php } ?>
            <div class="nr-main-content">
                <?php require_once __DIR__ . '/form.php'; ?>
            </div>
        </div>
    </div>
</div>
