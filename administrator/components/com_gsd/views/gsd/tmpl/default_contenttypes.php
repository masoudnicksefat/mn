<?php 
    $url = JURI::base() . 'index.php?option=com_gsd&view=items';
?>

<div class="nr-box-title">
    <a href="<?php echo $url ?>"><?php echo JText::_('GSD_ITEMS_OVERVIEW'); ?></a>
    <div><?php echo JText::_('GSD_ITEMS_OVERVIEW_DESC'); ?></div>
</div>
<div class="nr-box-content">
    <table class="nr-app-stats">
        <?php foreach ($this->stats['items'] as $key => $item) { ?>
        <tr>
            <td>
                <?php echo JText::_('GSD_' . $key); ?>
                <div class="bar"><span style="width:<?php echo $item['share']; ?>%"></span></div>
            </td>
            <td width="12%" class="text-center"><?php echo $item['count']; ?></td>
            <td width="12%" class="text-center"><?php echo $item['share']; ?>%</td>
        </tr>
        <?php } ?>
    </table>
</div>
