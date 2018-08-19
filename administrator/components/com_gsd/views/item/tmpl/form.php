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

<form action="<?php echo JRoute::_('index.php?option=com_gsd&view=edit&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span9">
                <?php echo $this->form->renderFieldSet("top"); ?>
                <?php echo $this->form->renderFieldSet("contenttypes"); ?>
            </div>
            <div class="span3 form-vertical">
                <?php echo $this->form->renderFieldSet("main"); ?>
            </div>
        </div>
    </div>

    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
</form>
