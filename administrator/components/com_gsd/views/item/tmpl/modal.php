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

<div class="container-popup">
    <?php $this->setLayout('edit'); ?>
    <?php echo $this->loadTemplate(); ?>
</div>

<div class="hidden">
    <button id="applyBtn" type="button" onclick="Joomla.submitbutton('item.apply');"></button>
    <button id="saveBtn" type="button" onclick="Joomla.submitbutton('item.save');"></button>
    <button id="closeBtn" type="button" onclick="Joomla.submitbutton('item.cancel');"></button>
</div>