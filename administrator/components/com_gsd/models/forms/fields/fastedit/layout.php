

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

JHtml::_('jquery.framework');
JHtml::_('bootstrap.popover');

extract($displayData);

?>

<table class="table table-striped nrTable">
    <thead>
        <tr>
            <th width="1%"></th>
            <th></th>
            <th width="1%"></th>
            <th width="22%"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $key => $item) { ?>
            <tr data-pk="<?php echo $item->id ?>">
                <td><span class="icon-<?php echo $item->state == 1 ? "publish" : "unpublish" ?>"></span></td>
                <td><?php echo JText::_('GSD_' . $item->contenttype); ?></td>
                <td class="gsdID">#<?php echo $item->id ?></td>
                <td class="btn-toolbar text-right">
                    <a href="#gsdModal"
                        data-src="<?php echo JRoute::_('index.php?option=com_gsd&tmpl=component&layout=modal&task=item.edit&id='. $item->id) ?>" 
                        class="btn"
                        data-toggle="modal"
                        title="<?php echo JText::_('GSD_EDIT_SNIPPET'); ?>">
                        <span class="icon-edit"></span>
                    </a>
                    <a href="#" class="btn gsdRemove" title="<?php echo JText::_('GSD_DELETE_SNIPPET'); ?>">
                        <span class="icon-trash"></span>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



