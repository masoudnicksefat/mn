<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;
JHtml::_('bootstrap.popover');
require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNR_PRO extends NRFormField
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    protected function getInput()
    {   
        $url = $this->getURL();

        return '<a style="float:none;" class="btn btn-danger ' . $this->get("class") . '" href="' . $url . '" target="_blank"><span class="icon-lock"></span> '. $this->prepareText($this->get("link", "NR_UPGRADE_TO_PRO_TO_UNLOCK")) .'</a>';
    }

    /**
     *  Constructs URL with Google Analytics Campaign Parameters
     *
     *  @return  string
     */
    private function getURL()
    {
        $url = $this->get("url");

        if (!$this->get("addutm", true))
        {
            return $url;
        }

        $utm  = 'utm_source=Joomla&utm_medium=upgradebutton&utm_campaign=freeversion';
        $char = strpos($url, "?") === false ? "?" : "&";

        return $url . $char . $utm;
    }
}