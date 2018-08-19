<?php 
    $url = JURI::base() . 'index.php?option=com_gsd&view=config&layout=edit#globaldata';
?>

<div class="nr-box-title">
    <a href="<?php echo $url ?>"><?php echo JText::_('GSD_GLOBAL_DATA'); ?> Overview</a>
    <div><?php echo JText::_('GSD_STATUS_OF_SD'); ?></div>
</div>
<div class="nr-box-content">
    <table class="nr-app-stats">
        <?php foreach ($this->stats['siteData'] as $key => $item) { ?>
        <tr>
            <td><?php echo JText::_($key); ?></td>
            <td width="12%" class="text-right">
                <span class="icon-<?php echo $item ? "publish" : "unpublish" ?>"></span>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
