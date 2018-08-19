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

class JFormFieldImagePlus extends GSDMultiField
{
    /**
     *  Fields XML
     *
     *  @return  string
     */
    public function getFieldsXML()
    {
        return '
            <field name="option" type="list"
                label="NR_IMAGE"
                description="GSD_IMAGE_OPTION_DESC"
                default="1">
                <option value="1">JGLOBAL_INHERIT</option>
                <option value="2">NR_UPLOAD</option>
                <option value="3">NR_CUSTOMURL</option>
            </field>
            <field name="file" type="media" 
                label="GSD_IMAGE_UPLOAD"
                description="GSD_IMAGE_UPLOAD_DESC"
                showon="option:2"
                preview="tooltip"
                validate="recommended"
                message="GSD_REQUIRED"
                class="input-xlarge"
            />
            <field name="url" type="url"
                label="GSD_IMAGE_CUSTOM"
                description="GSD_IMAGE_CUSTOM_DESC"
                hint="' . JURI::root() . 'image.jpg"
                class="input-xxlarge"
                showon="option:3"
                validate="recommended"
                message="GSD_REQUIRED"
            />
        ';
    }
}