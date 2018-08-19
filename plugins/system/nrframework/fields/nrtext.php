<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldNRText extends JFormFieldText
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    function getInput()
    {   
        // Adds an extra info label next to input
        $addon = (string) $this->element['addon'];

        if (!empty($addon))
        {
            $html[] = '<div class="input-append input-group">';
            $html[] = parent::getInput();
            $html[] = '<span class="add-on input-group-addon">'.JText::_($addon).'</span>';
            $html[] = '</div>';
        } else
        {
            $html[] = parent::getInput();
        }

        // Adds a link next to input
        $url        = $this->element['url'];
        $text       = $this->element['urltext'];
        $target     = $this->element['urltarget'] ? $this->element['urltarget'] : "_blank";
        $class      = $this->element['urlclass'] ? $this->element['urlclass'] : "";
        $attributes = "";

        // Popup mode
        if ($this->element["urlpopup"])
        {
            $class .= " nrPopup";
            $attributes = 'data-width="600" data-height="600"';
            $this->addPopupScript();
        }

        if ($url && $text)
        {
            $html[] = '<a ' . $attributes . ' class="' . $class . '" style="margin-left:10px;" href="' . $url . '" target="' . $target . '">' . JText::_($text) . '</a>';
        }

        return implode(" ", $html);
    }

    private function addPopupScript()
    {
        static $run;

        if ($run)
        {
            return;
        }

        $run = true;

        JFactory::getDocument()->addScriptDeclaration('
            jQuery(function($) {
                $(".nrPopup").click(function() {
                    url    = $(this).attr("href");
                    width  = $(this).data("width");
                    height = $(this).data("height");

                    window.open(""+url+"", "nrPopup", "width=" + width + ", height=" + height + "");

                    return false;              
                })
            })
        ');
    }
}