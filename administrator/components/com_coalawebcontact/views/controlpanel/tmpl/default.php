<?php
defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Component
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
JHtml::_('jquery.framework');
$user = JFactory::getUser();
$lang = JFactory::getLanguage();

$doc= JFactory::getDocument();
$doc->addScript(JURI::root(true) . '/media/coalawebcontact/components/contact/js/sweetalert.min.js');
$doc->addStyleSheet(JURI::root(true) . '/media/coalawebcontact/components/contact/css/sweetalert.css')
?>

<?php if ($this->needsdlid): ?>
    <div id="dlid" class="well">
        <div class="row-fluid">
            <?php echo JText::_('COM_CWCONTACT_NODOWNLOADID_GENERAL_MESSAGE'); ?>

            <form name="dlidform" action="index.php" method="post" class="form-inline">


                <input type="text" name="dlid" placeholder="<?php echo JText::_('COM_CWCONTACT_DLID') ?>" class="input-xlarge">
                <button type="submit" class="btn btn-info">
                    <span class="icon icon-unlock"></span>
                    <?php echo JText::_('COM_CWCONTACT_DLID_BTN') ?>
                </button>
                <input type="hidden" name="option" value="com_coalawebcontact" />
                <input type="hidden" name="view" value="controlpanel" />
                <input type="hidden" name="task" value="controlpanel.applydlid" />
                <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken() ?>" value="1" />
            </form>
        </div>
    </div>
<?php endif; ?>
<div id="cpanel-v2" class="span8 well">
    <div class="row-fluid">
        
    <?php if ($this->isPro): ?>
        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
                <a class="green-light" href="index.php?option=com_coalawebcontact&view=customfields">
                    <img alt="<?php echo JText::_('COM_CWCONTACT_VIEW_CUSTOMFIELDS_TITLE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-list-v2.png" />
                    <span><?php echo JText::_('COM_CWCONTACT_VIEW_CUSTOMFIELDS_TITLE'); ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->isPro): ?>
        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
                <a class="aqua-light" href="index.php?option=com_coalawebcontact&view=emailtemplates">
                    <img alt="<?php echo JText::_('COM_CWCONTACT_VIEW_EMAILTEMPLATES_SHORT_TITLE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-list-v2.png" />
                    <span><?php echo JText::_('COM_CWCONTACT_VIEW_EMAILTEMPLATES_SHORT_TITLE'); ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>
        
   <?php if ($this->isPro): ?>
        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
                <a class="orange-dark optimize" href="index.php?option=com_coalawebcontact&task=controlpanel.optimize">
                    <img alt="<?php echo JText::_('COM_CWCONTACT_TITLE_OPTIMIZE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-speed-v2.png" />
                    <span><?php echo JText::_('COM_CWCONTACT_TITLE_OPTIMIZE'); ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
        <div class="icon">
            <a class="red-light" onclick="Joomla.popupWindow('https://coalaweb.com/support/documentation/item/coalaweb-contact-guide', 'Help', 700, 500, 1);" href="#">
                <img alt="<?php echo JText::_('COM_CWCONTACT_TITLE_HELP'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-support-v2.png" />
                <span><?php echo JText::_('COM_CWCONTACT_TITLE_HELP'); ?></span>
            </a>
        </div>
    </div>

    <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
        <div class="icon">
                <a class="blue-light" href="index.php?option=com_config&view=component&component=com_coalawebcontact">
                    <img alt="<?php echo JText::_('COM_CWCONTACT_TITLE_OPTIONS'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-options-v2.png" />
                    <span><?php echo JText::_('COM_CWCONTACT_TITLE_OPTIONS'); ?></span>
                </a>
        </div>
    </div>
    
    <?php if (!$this->isPro): ?>
        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
                <a class="pink-light" onclick="Joomla.popupWindow('https://coalaweb.com/extensions/joomla-extensions/coalaweb-contact/feature-comparison', 'Help', 700, 500, 1)" href="#">
                    <img alt="<?php echo JText::_('COM_CWCONTACT_TITLE_UPGRADE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-upgrade-v2.png" />
                    <span><?php echo JText::_('COM_CWCONTACT_TITLE_UPGRADE'); ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>
</div>

<div id="tabs" class="span4">
    <div class="row-fluid">

    <?php
    $options = array(
        'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
        'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
        'startOffset' => 0, // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
        'startTransition' => 1,
    );
    ?>

    <?php echo JHtml::_('sliders.start', 'slider_group_id', $options); ?>



    <?php echo JHtml::_('sliders.panel', JText::_('COM_CWCONTACT_SLIDER_TITLE_ABOUT'), 'slider_1_id'); ?>
    <div class="cw-slider well well-small">
        <?php if ($this->isPro): ?>
            <h3><?php echo JText::_('COM_CWCONTACT_TITLE_PRO'); ?></h3>
            <?php echo JText::_('COM_CWCONTACT_ABOUT_DESCRIPTION'); ?>
        <?php else : ?>
            <h3><?php echo JText::_('COM_CWCONTACT_TITLE_CORE'); ?></h3>
            <?php echo JText::_('COM_CWCONTACT_ABOUT_DESCRIPTION'); ?>
        <?php endif; ?>
    </div>
    
    <?php echo JHtml::_('sliders.panel', JText::_('COM_CWCONTACT_SLIDER_TITLE_SUPPORT'), 'slider_2_id'); ?>

    <div class="cw-slider well well-small">
        <?php echo JText::_('COM_CWCONTACT_SUPPORT_DESCRIPTION'); ?>
    </div>

    <?php echo JHtml::_('sliders.panel', JText::_('COM_CWCONTACT_SLIDER_TITLE_VERSION'), 'slider_3_id'); ?>

    <?php $type = ($this->isPro ? JText::_('COM_CWCONTACT_RELEASE_TYPE_PRO') : JText::_('COM_CWCONTACT_RELEASE_TYPE_CORE')); ?>

     <div class="cw-slider well well-small">
        <h3> <?php echo JText::_('COM_CWCONTACT_RELEASE_TITLE'); ?> </h3>
        <ul class="cw-slider">
            <li>  <?php echo JText::_('COM_CWCONTACT_FIELD_RELEASE_TYPE_LABEL'); ?>  <strong><?php echo $type; ?> </strong></li>
            <li>   <?php echo JText::_('COM_CWCONTACT_FIELD_RELEASE_VERSION_LABEL'); ?> <strong> <?php echo $this->version?> </strong></li>
            <li>  <?php echo JText::_('COM_CWCONTACT_FIELD_RELEASE_DATE_LABEL'); ?>  <strong> <?php echo $this->release_date; ?>  </strong></li>
        </ul>
    </div>
        
    <?php if (!$this->isPro): ?>
        <?php echo JHtml::_('sliders.panel', JText::_('COM_CWCONTACT_SLIDER_TITLE_UPGRADE'), 'slider_4_id'); ?>
         <div class="cw-slider well well-small">
            <div class="cw-message-block">
                <div class="cw-message">
                    <p class="upgrade"><?php echo JText::_('COM_CWCONTACT_MSG_UPGRADE'); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
        

    <?php echo JHtml::_('sliders.end'); ?>       
</div>
</div>
<script>
  jQuery.noConflict();
  
  jQuery('a.optimize').click(function(e) {
    e.preventDefault(); // Prevent the href from redirecting directly
    var linkURL = jQuery(this).attr("href");
    warnBeforeOptimize(linkURL);
  });

  
 function warnBeforeOptimize(linkURL) {
    swal({
      title: "<?php echo JText::_('COM_CWCONTACT_OPTIMIZE_POPUP_TITLE'); ?>", 
      text: "<?php echo JText::_('COM_CWCONTACT_OPTIMIZE_POPUP_MSG'); ?>", 
      type: "warning",
      html: true,
      showCancelButton: true
    }, function() {
      // Redirect the user
      window.location.href = linkURL;
    });
  }
  
  </script>

