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

class JFormFieldRating extends GSDMultiField
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
                label="GSD_RATING"
                description="GSD_RATING_DESC"
                default="1">';

        if (!$this->get("hidedisabled", false))
        {
            $xml .= '<option value="0">JDISABLED</option>';
        }

        if (!$this->get("hideinherit", false))
        {
            $xml .= '<option value="1">JGLOBAL_INHERIT</option>';
        }

        $xml .= '
                <option value="2">Custom</option>
            </field>';

        // Rating Value Field
        $xml .= '
            <field name="ratingValue" type="nr_rate"
                label="GSD_RATE_VALUE"
                description="GSD_RATE_VALUE_DESC"
                default="5"
                showon="option:2"
            />';

        // Reviews Count Field
        if (!$this->get("hidecount", false))
        {
            $xml .= '
                <field name="reviewCount" type="number"
                    label="GSD_REVIEWS_QUANTITY"
                    description="GSD_REVIEWS_QUANTITY_DESC"
                    default="1"
                    min="1"
                    step="1"
                    class="input-mini"
                    showon="option:2"
                />
            ';
        }

        return $xml;
    }
}