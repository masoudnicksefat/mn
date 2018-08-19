<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

use Joomla\Registry\Registry;

class Functions
{
    /**
     *  Log message to framework's log file
     *
     *  @param   mixed  $data    Log message
     *
     *  @return  void
     */
    public static function log($data)
    {
        $data = (is_object($data) || is_array($data)) ? print_r($data, true) : $data;

        \JLog::add($data, \JLog::DEBUG, 'nrframework');
    }

    /**
     *  Return's a URL with the Google Analytics Campaign Parameters appended to the end
     *
     *  @param   string  $url       The URL
     *  @param   string  $medium    Campaign Medium
     *  @param   string  $campaign  Campaign Name
     *
     *  @return  string
     */
    public static function getUTMURL($url, $medium = "upgradebutton", $campaign = "freeversion")
    {
        if (!$url)
        {
            return;
        }

        $utm  = 'utm_source=Joomla&utm_medium=' . $medium . '&utm_campaign=' . $campaign;
        $char = strpos($url, "?") === false ? "?" : "&";

        return $url . $char . $utm;
    }

    /**
     *  Returns user's Download Key
     *
     *  @return  string
     */
    public static function getDownloadKey()
    {
        $class = new Updatesites();
        return $class->getDownloadKey();
    }

    /**
     *  Adds a script or a stylesheet to the document
     *
     *  @param  Mixed    $files           The files to be to added to the document
     *  @param  boolean  $appendVersion   Adds file versioning based on extension's version
     *
     *  @return void
     */
    public static function addMedia($files, $extension = "plg_system_nrframework", $appendVersion = true)
    {
        $doc       = \JFactory::getDocument();
        $version   = self::getExtensionVersion($extension);
        $mediaPath = \JURI::root(true) . "/media/" . $extension;

        if (!is_array($files))
        {
            $files = array($files);
        }

        foreach ($files as $key => $file)
        {
            $fileExt  = \JFile::getExt($file);
            $filename = $mediaPath . "/" . $fileExt . "/" . $file;
            $filename = ($appendVersion) ? $filename . "?v=" . $version : $filename;

            if ($fileExt == "js")
            {
                $doc->addScript($filename);
            }

            if ($fileExt == "css")
            {
                $doc->addStylesheet($filename);
            }
        }
    }

    /**
     *  Get the Framework version
     *
     *  @return  string  The framework version
     */
    public static function getVersion()
    {
        return self::getExtensionVersion("plg_system_nrframework");
    }

    /**
     *  Checks if document is a feed document (xml, rss, atom)
     *
     *  @return  boolean
     */
    public static function isFeed()
    {
        return (
            \JFactory::getDocument()->getType() == 'feed'
            || \JFactory::getDocument()->getType() == 'xml'
            || \JFactory::getApplication()->input->getWord('format') == 'feed'
            || \JFactory::getApplication()->input->getWord('type') == 'rss'
            || \JFactory::getApplication()->input->getWord('type') == 'atom'
        );
    }

    public static function loadLanguage($extension = 'plg_system_nrframework', $basePath = '')
    {
        if ($basePath && \JFactory::getLanguage()->load($extension, $basePath))
        {
            return true;
        }

        $basePath = self::getExtensionPath($extension, $basePath, 'language');

        return \JFactory::getLanguage()->load($extension, $basePath);
    }

    /**
     *  Returns extension ID
     *
     *  @param   string  $extension  Extension name
     *
     *  @return  integer
     */
    public static function getExtensionID($extension, $folder = null)
    {
        $db = \JFactory::getDBO();

        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($extension));

        if ($folder)
        {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        }

        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     *  Checks if extension is installed
     *
     *  @param   string  $extension  The extension element name
     *  @param   string  $type       The extension's type 
     *  @param   string  $folder     Plugin folder
     *
     *  @return  boolean             Returns true if extension is installed
     */
    public static function extensionInstalled($extension, $type = 'component', $folder = 'system')
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $db = \JFactory::getDbo();

        switch ($type)
        {
            case 'component':

                $result = $db->setQuery(
                    $db->getQuery(true)
                        ->select('COUNT(' . $db->quoteName('extension_id') . ')')
                        ->from($db->quoteName('#__extensions'))
                        ->where($db->quoteName('element') . ' = ' . $db->quote('com_'.$extension))
                        ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
                        ->where($db->quoteName('enabled') . ' = 1')
                )->loadResult();

                if ($result)
                {
                    return true;
                }

                break;

            case 'plugin':
                return \JFile::exists(JPATH_PLUGINS . '/' . $folder . '/' . $extension . '/' . $extension . '.php');

            case 'module':
                return (\JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/' . $extension . '.php')
                    || \JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
                    || \JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/' . $extension . '.php')
                    || \JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
                );

            case 'library':
                return \JFolder::exists(JPATH_LIBRARIES . '/' . $extension);
        }

        return false;
    }

    /**
     *  Returns the version number from the extension's xml file
     *
     *  @param   string  $extension  The extension element name
     *
     *  @return  string              Extension's version number
     */
    public static function getExtensionVersion($extension, $type = false)
    {
        $hash  = MD5($extension . "_" . ($type ? "1" : "0"));
        $cache = Cache::read($hash);

        if ($cache)
        {
            return $cache;
        }

        $xml = self::getExtensionXMLFile($extension);

        if (!$xml)
        {
            return false;
        }

        $xml = \JInstaller::parseXMLInstallFile($xml);

        if (!$xml || !isset($xml['version']))
        {
            return '';
        }

        $version = $xml['version'];

        if ($type)
        {
            $extType = (self::extensionHasProInstalled($extension)) ? "Pro" : "Free";
            $version = $xml["version"] . " " . $extType;
        }

        return Cache::set($hash, $version);
    }

    public static function getExtensionXMLFile($extension, $basePath = JPATH_ADMINISTRATOR)
    {
        $alias = explode("_", $extension);
        $alias = end($alias);

        $filename = (strpos($extension, 'mod_') === 0) ? "mod_" . $alias : $alias;
        $file = self::getExtensionPath($extension, $basePath) . "/" . $filename . ".xml";

        if (\JFile::exists($file))
        {
            return $file;
        }
        
        return false;
    }

    public static function extensionHasProInstalled($extension)
    {
        static $result;

        if ($result)
        {
            return $result;
        }

        // Path to extension's version file
        $versionFile = self::getExtensionPath($extension) . "/version.php";
        $NR_PRO = true;

        // If version file does not exist we assume we have a PRO version installed
        if (file_exists($versionFile))
        {
            require_once($versionFile);
        }

        return ($result = (bool) $NR_PRO);
    }

    public static function getExtensionPath($extension = 'plg_system_nrframework', $basePath = JPATH_ADMINISTRATOR, $check_folder = '')
    {
        if (!in_array($basePath, array('', JPATH_ADMINISTRATOR, JPATH_SITE)))
        {
            return $basePath;
        }

        switch (true)
        {
            case (strpos($extension, 'com_') === 0):
                $path = 'components/' . $extension;
                break;

            case (strpos($extension, 'mod_') === 0):
                $path = 'modules/' . $extension;
                break;

            case (strpos($extension, 'plg_system_') === 0):
                $path = 'plugins/system/' . substr($extension, strlen('plg_system_'));
                break;

            case (strpos($extension, 'plg_editors-xtd_') === 0):
                $path = 'plugins/editors-xtd/' . substr($extension, strlen('plg_editors-xtd_'));
                break;
        }

        $check_folder = $check_folder ? '/' . $check_folder : '';

        if (is_dir($basePath . '/' . $path . $check_folder))
        {
            return $basePath . '/' . $path;
        }

        if (is_dir(JPATH_ADMINISTRATOR . '/' . $path . $check_folder))
        {
            return JPATH_ADMINISTRATOR . '/' . $path;
        }

        if (is_dir(JPATH_SITE . '/' . $path . $check_folder))
        {
            return JPATH_SITE . '/' . $path;
        }

        return $basePath;
    }

    public static function loadModule($id, $moduleStyle = null)
    {  
        // Return if no module id passed
        if (!$id) 
        {
            return;
        }

        // Fetch module from db
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__modules')
            ->where('id='.$db->q($id));

        $db->setQuery($query);

        // Return if no modules found
        if (!$module = $db->loadObject()) 
        {
            return;
        }

        // Success! Return module's html
        return \JModuleHelper::renderModule($module, $moduleStyle);
    }

    public static function fixDate(&$date)
    {
        if (!$date)
        {
            $date = null;

            return;
        }

        $date = trim($date);

        // Check if date has correct syntax: 00-00-00 00:00:00
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+( [0-9][0-9]:[0-9][0-9]:[0-9][0-9])?$#', $date))
        {
            return;
        }

        // Check if date has syntax: 00-00-00 00:00
        // If so, add :00 (seconds)
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+ [0-9][0-9]:[0-9][0-9]$#', $date))
        {
            $date .= ':00';

            return;
        }

        // Check if date has a prepending date syntax: 00-00-00 ...
        // If so, add 00:00:00 (hours:mins;secs)
        if (preg_match('#^([0-9]+-[0-9]+-[0-9]+)#', $date, $match))
        {
            $date = $match['1'] . ' 00:00:00';

            return;
        }

        // Date format is not correct, so return null
        $date = null;
    }

    public static function fixDateOffset(&$date)
    {
        if ($date <= 0)
        {
            $date = 0;

            return;
        }

        $date = \JFactory::getDate($date, \JFactory::getUser()->getParam('timezone', \JFactory::getConfig()->get('offset')));
        $date->setTimezone(new \DateTimeZone('UTC'));

        $date = $date->format('Y-m-d H:i:s', true, false);
    }

    // Text
    public static function clean($string) 
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function dateTimeNow() 
    {
        return \JFactory::getDate()->format("Y-m-d H:i:s");
    }

    /**
     *  Get framework plugin's parameters
     *
     *  @return  JRegistry   The plugin parameters
     */
    public static function params()
    {
        $hash = md5('frameworkParams');

        if (Cache::has($hash))
        {
            return Cache::read($hash);
        }

        $db = \JFactory::getDBO();

        $result = $db->setQuery(
            $db->getQuery(true)
            ->select('params')
            ->from('#__extensions')
            ->where('element = ' . $db->quote('nrframework'))
        )->loadResult();

        return Cache::set($hash, new Registry($result));
    }

    public static function loadReCaptcha()
    {
        \JHtml::_(
            'script', 
            'plg_system_nrframework/recaptcha.js', 
            array('version' => 'auto', 'relative' => true)
        );

        \JHtml::_(
            'script', 
            '//www.google.com/recaptcha/api.js?onload=NRInitReCaptcha&render=explicit&hl=' . \JFactory::getLanguage()->getTag(), 
            array(), 
            array('async' => true, 'defer' => true)
        );
    }
}

?>