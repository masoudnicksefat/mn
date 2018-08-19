<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
// 
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAcymailing extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'acymailing';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions() 
    {
        if (!$lists = $this->getList())
        {
            return;
        }

        $options = array();

        foreach ($lists as $option)
        {
            $options[] = JHTML::_('select.option', $option->listid, $option->name);
        }

        return array_merge(parent::getOptions(), $options);
    }

    /**
     *  Retrieve all AcyMailing lists using Acymailing API
     *
     *  @return  array  AcyMailing lists
     */
    private function getList()
    {
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php'))
        {
            return;
        }
         
        $listClass = acymailing_get('class.list');
        return $listClass->getLists();
    }
}