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

require_once JPATH_PLUGINS . '/system/nrframework/helpers/field.php';

class JFormFieldThings extends NRFormField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        $context     = $this->get("context");
        $isDisabled  = $this->get('disabled', false);
        $contextName = "";
        $thingTitle  = "";

        if ($this->value)
        {
            $contextName = JText::_('PLG_GSD_' . strtoupper($context) . '_ALIAS');
            $thingTitle  = GSDHelper::getThingTitle($this->value, $context);
        }
 
        // Include jQuery
        JHtml::_('jquery.framework');

        $this->doc->addStyleDeclaration('
            .gsdThing {
                display:inline-flex;
                display:-webkit-inline-flex;
                align-items:center;
                -webkit-align-items:center;
                text-decoration:none !important;
                border-radius: 3px;
                background-color: #fff;
            }
            .gsdThing .val {
                pointer-events:none;
                border: solid 1px #c3c3c3;
                padding: 5px 10px;
                border-radius: 3px 0 0 3px;
                color:#333;
                min-width:400px;
            }
            .gsdThing .btn {
                border-color:#c3c3c3;
                padding: 5px 10px;
                border-radius: 0 3px 3px 0;
                margin-left: -1px;
            }
            .gsdThing .val span {
                opacity:.5;
            }
            .gsdThing.disabled {
                background-color:#eee;
            }
        ');

        $thingLabel = $this->value ? $contextName . ': ' . $thingTitle : '<span>' . JText::_('GSD_PLEASE_SELECT_ITEM') . '</span>';

        $html[] = '
            <a href="#thingModal" data-toggle="modal" class="gsdThing ' . ($isDisabled ? "disabled" : "") . '">
                <span class="val">' . $thingLabel . '</span>
                <span class="btn">
                    <span class="icon-list icon-white"></span> ' . JText::_('JSELECT') .'
                </span>
            </a>
        ';

        if (!$isDisabled)
        {
            $modalURL = 'index.php?option=com_gsd&view=things&tmpl=component';
            if ($context)
            {
                $modalURL .= '&filter_context=' . $context;
            }
            
            $modalOptions = array(
                'url'        => JRoute::_($modalURL),
                'title'      => JText::_('GSD_ITEMS'),
                'width'      => '800px',
                'height'     => '300px',
                'modalWidth' => '80',
                'bodyHeight' => '70',
                'footer'     => '<a type="button" class="btn" data-dismiss="modal">'. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
            );

            $html[] = JHtml::_('bootstrap.renderModal', 'thingModal', $modalOptions);

        }

        $html[] = '<input id="thingid" class="input-small" type="hidden" name="' . $this->name . '" value="'
            . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';

        return implode("", $html);
    }
}