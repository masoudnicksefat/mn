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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/multifield.php';

class JFormFieldAuthorName extends GSDMultiField
{
    /**
     *  Fields XML
     *
     *  @return  string
     */
    public function getFieldsXML()
    {
        // Options Field
        $xml = '
            <field name="option" type="list"
                label="GSD_AUTHOR"
                description="GSD_AUTHOR_DESC"
                default="0">';

        if (!$this->get("hideinherit", false))
        {
            $xml .= '<option value="0">JGLOBAL_INHERIT</option>';
        }

        $xml .= '
                <option value="1">GSD_AUTHOR_SELECT</option>
                <option value="2">GSD_AUTHOR_SET</option>
            </field>
            <field name="user" type="user"
                label="GSD_AUTHOR_SELECT"
                description="GSD_AUTHOR_SELECT_DESC"
                showon="option:1"
            />
            <field name="custom" type="text"
                label="GSD_AUTHOR_SET"
                description="GSD_AUTHOR_SET_DESC"
                hint="John Doe"
                showon="option:2"
            />';

        return $xml;
    }
}