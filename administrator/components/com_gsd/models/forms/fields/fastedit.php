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

JLoader::register('NRFormField', JPATH_PLUGINS . '/system/nrframework/helpers/field.php');

class JFormFieldFastEdit extends NRFormField
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        $thing = $this->get("thing", 0);
        $error = $this->get('error', 'GSD_SAVE_FIRST');
        $html  = '';

        $this->doc->addStyleSheet(JURI::base(true) . '/components/com_gsd/models/forms/fields/fastedit/fastedit.css');

        // In order to be able to assosiate a snippet the item must be saved first.
        if (!$thing)
        {
            return $html . '<div class="alert alert-info gsdFastEdit">' . JText::_($error) . '</div>';
        }

        // Cool. The item is saved.
        $plugin = $this->get("plugin", null);
        $addURL = JRoute::_('index.php?option=com_gsd&tmpl=component&layout=modal&view=item&thing=' . $thing . '&plugin=' . $plugin);

        // Add Media
        $this->doc->addScript(JURI::base(true) . '/components/com_gsd/models/forms/fields/fastedit/fastedit.js');

        // Add language strings used by the JS plugin
        JText::script('GSD_ADD_SNIPPET');
        JText::script('GSD_EDIT_SNIPPET');
        JText::script('NR_ARE_YOU_SURE');

        $html .= '
            <div class="gsdFastEdit" data-thing="'. $thing .'" data-plugin="'. $plugin .'" data-base="'. JURI::base(true) .'">
                ' . $this->renderModal() . '
                <a href="#gsdModal" class="btn btn-success add" data-toggle="modal" data-src="'. $addURL . '">
                    <span class="icon-new"></span>'
                    . JText::_('GSD_ADD_SNIPPET') .'
                </a>
                <div class="items"></div>
            </div>';

        return $html;
    }

    /**
     *  Render the modal is going to be used by all buttons
     *
     *  @return  string
     */
    private function renderModal()
    {
        $options = array(
            'title'       => JText::_('GSD_EDIT_SNIPPET'),
            'url'         => "#",
            'height'      => '400px',
            'width'       => '800px',
            'backdrop'    => 'static',
            'bodyHeight'  => '70',
            'modalWidth'  => '70',
            'footer'      => '<a type="button" class="btn" data-dismiss="modal" aria-hidden="true"'
                    . ' onclick="jQuery(\'#gsdModal iframe\').contents().find(\'#closeBtn\').click();">'
                    . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
                    . '<button type="button" class="btn btn-primary" aria-hidden="true"'
                    . ' onclick="jQuery(\'#gsdModal iframe\').contents().find(\'#saveBtn\').click();">'
                    . JText::_('JSAVE') . '</button>'
                    . '<button type="button" class="btn btn-success" aria-hidden="true"'
                    . ' onclick="jQuery(\'#gsdModal iframe\').contents().find(\'#applyBtn\').click();">'
                    . JText::_('JAPPLY') . '</button>',
        );

        return JHtml::_('bootstrap.renderModal', 'gsdModal', $options);
    }
}