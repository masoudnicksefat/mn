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

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldContentTypes extends NRFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $contentTypes = GSDHelper::getContentTypes();

        if ($this->get("showselect", 'false') === 'true')
        {
            $options[] = JHTML::_('select.option', '', '- ' . JText::_('GSD_CONTENT_TYPE_SELECT') . ' -');
        }

        foreach ($contentTypes as $contentType)
        {
            $options[] = JHTML::_('select.option', $contentType, JText::_('GSD_' . strtoupper($contentType)));
        }

        return array_merge(parent::getOptions(), $options);
    }

    protected function getInput()
    {
        if (!$this->get("showhelp", false))
        {
            return parent::getInput();
        }

        $html[] = parent::getInput();
        $html[] = '<a class="btn contentTypeHelp" target="_blank" title="' . JText::_("GSD_CONTENTTYPE_HELP") . '"><span class="icon-help"></span></a>';

        $this->doc->addScriptDeclaration('
            jQuery(function($) {
                $("#' . $this->id . '").on("change", function() {
                    href = "http://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/the-" + $(this).val() + "s-snippet";
                    $(".contentTypeHelp").attr("href", href);
                }).trigger("change");
            })
        ');

        return implode('', $html);
    }
}