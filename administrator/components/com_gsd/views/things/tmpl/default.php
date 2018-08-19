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

JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$context   = $this->state->get('filter.context');

JFactory::getDocument()->addStyleDeclaration('
    .js-stools-field-filter > * {
        margin:0;
    }
');

?>

<div class="nr-app">
    <form action="<?php echo JRoute::_('index.php?option=com_gsd&view=things'); ?>" 
        class="things"
        method="post"
        name="adminForm"
        id="adminForm"
        data-context="<?php echo $context; ?>"
        data-context-name="<?php echo JText::_('PLG_GSD_' . strtoupper($context) . '_ALIAS') ?>"
        >
        <?php
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

        <?php if ($this->hasAutoMode) { ?>
            <div class="alert alert-info text-center" style="margin:10px 0;">
                <?php echo JText::sprintf('GSD_AUTOMODE_ALERT', ucfirst($context), ucfirst($context)); ?>
                <a class="btn btn-primary" target="_blank" href="http://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/the-<?php echo $context; ?>-integration">
                    <?php echo JText::_('NR_READMORE'); ?>
                </a>
            </div>
        <?php } ?>

        <table class="adminlist nrTable table">
            <thead>
                <tr>
                    <th class="center hide" width="2%"><?php echo JHtml::_('grid.checkall'); ?></th>
                    <th width="3%" class="nowrap hidden-phone" align="center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('searchtools.sort', 'NR_TITLE', 'a.item', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="text-center nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->items)) { ?>
                    <?php foreach($this->items as $i => $item): ?>
                        <tr class="row<?php echo $i % 2; ?>" data-id="<?php echo $item->id ?>">
                            <td class="hide"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                            <td class="center">
                                <span class="icon-<?php echo $item->state ? "publish" : "unpublish" ?>"></span>
                            </td>
                            <td>
                                <a class="setThing" href="#"><?php echo $item->title; ?></a>
                            </td>
                            <td class="text-center"><?php echo $item->id ?></td>
                        </tr>
                    <?php endforeach; ?>  
                <?php } else { ?>
                    <tr>
                        <td align="center" colspan="9">
                            <div align="center">
                                <?php echo JText::_('NR_NO_ITEMS_FOUND'); ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>        
            </tbody>
            <tfoot>
    			<tr><td colspan="9" style="text-align:center;"><?php echo $this->pagination->getListFooter(); ?></td></tr>
            </tfoot>
        </table>
        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="tmpl" value="component" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
        </div>
    </form>
</div>

<script>
jQuery(function($) {

    // Manipulate Filters Bar
    $("#filter_context").parent().prependTo('.js-stools-container-bar');
    $(".js-stools-btn-filter").parent().remove();
    $(".js-stools-container-filters").remove();

    // Setup Click Hanlder
    $(".setThing").click(function() {

        // Set thing id and context value
        window.parent.jQuery('#thingid').val($(this).closest("tr").data("id"));
        window.parent.jQuery('#jform_plugin').val($(".things").data("context"));

        // Set thing label
        window.parent.jQuery('.gsdThing > .val').html($(".things").data("context-name") + ": " + $(this).text());

        // Close Modal
        window.parent.jQuery('#thingModal').modal('hide');

        return false;
    })
})
</script>