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

require_once __DIR__ . '/script.install.helper.php';

class PlgGSDEasyBlogInstallerScript extends PlgGSDEasyBlogInstallerScriptHelper
{
    public $alias = 'easyblog';
    public $extension_type = 'plugin';
    public $plugin_folder = "gsd";
    public $show_message = false;
}