<div class="nr-box nr-box-hr">
    <div class="nr-box-title">
        <?php echo JText::_('GSD_SDTT'); ?>
        <div><?php echo JText::_('GSD_SDTT_DESC'); ?></div>
    </div>
    <div class="nr-box-content">
        <form class="gsdtt">
            <input id="url" required="true" type="text" placeholder="http://" value="<?php echo JURI::root(); ?>"/>
            <button class="btn btn-primary" type="submit"><?php echo JText::_('GSD_TEST'); ?></button>
        </form>
    </div>
</div>

<?php 
    JFactory::getDocument()->addScriptDeclaration('
        jQuery(function($) {
            $(".gsdtt").submit(function(event) {
                event.preventDefault();
                var base = "https://search.google.com/structured-data/testing-tool/u/0/#url=";
                var URL  = $(this).find("#url").val();
                window.open(base + URL);
            })
        })
    ');
?>