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

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nrframework/helpers/field.php';

class GSDMultiField extends NRFormField
{
    /**
     *  Return HTML Input
     *
     *  @return  string
     */
	protected function getInput()
	{
        $this->doc->addStyleDeclaration('
            .gsdMultiField {
                margin:0 0 -18px -180px;
            }
        ');

        $group_parts = explode(".", $this->group);
        $contentType = end($group_parts);
        $fieldName   = (string) $this->get("name");
        $this->group .= "." . $fieldName;

        $xml = new SimpleXMLElement('
            <fields name="' . $contentType . '">
                <fields name="' . $fieldName . '">
                    ' . $this->getFieldsXML() . '
                </fields>
            </fields>
        ');

        $this->form->setField($xml);

        // Render XML
        $html[] = '<div class="gsdMultiField">';

        foreach ($xml->fields->field as $key => $field)
        {
            $html[] = $this->form->renderField($field->attributes()->name, $this->group);
        }

        $html[] = '</div>';

        return implode("", $html);
    }
}