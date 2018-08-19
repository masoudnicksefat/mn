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

JLoader::register('GSDHelper', JPATH_ADMINISTRATOR . '/components/com_gsd/helpers/helper.php');

use NRFramework\Cache;
use Joomla\Registry\Registry;

/**
 *  Google Structured Data helper class
 */
class GSDPlugin extends JPlugin
{
    /**
     *  Auto load the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  Joomla Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  Joomla Database Object
     *
     *  @var  object
     */
    protected $db;

    /**
     *  Holds all available snippets for the current active page.
     *
     *  @var  array
     */
    protected $snippets;

    /**
     *  Database table name to search for things
     *
     *  @var  string
     */
    protected $table;

    /**
     *  ID Column Name
     *
     *  @var  string
     */
    protected $column_id = "id";

    /**
     *  Title column name
     *
     *  @var  string
     */
    protected $column_title = "title";

    /**
     *  State column name
     *
     *  @var  string
     */
    protected $column_state = "state";

    /**
     *  Indicates the query string parameter name that is used by the front-end component
     *
     *  @var  string
     */
    protected $thingRequestIDName = 'id';

    /**
     *  Indicates the request variable name used by plugin's assosiated component
     *
     *  @var  string
     */
    protected $thingRequestViewVar = 'view';

    /**
     *  Indicates the default content type which can be used to automatically produce structured data
     *
     *  @var  mixed
     */
    protected $defaultContentType = null;

    /**
     *  Event triggered to gather all available plugins.
     *  Mostly used by the dropdowns in the backend.
     *
     *  @param   boolean  $mustBeInstalled  If enabled, the assosiated component must be installed
     *
     *  @return  array
     */
    public function onGSDGetType($mustBeInstalled = true)
    {
        if ($mustBeInstalled && !NRFramework\Functions::extensionInstalled($this->_name))
        {
            return;
        }

        return array(
            "name"  => JText::_("PLG_GSD_" . strtoupper($this->_name) . "_ALIAS"),
            "alias" => $this->_name
        );
    }

    /**
     *  Event triggered by Things model to get plugin's list query
     *  Used in backend.
     *
     *  @param   JModel  $model    The Model Class
     *  @param   string  $context  The plugin's name
     *
     *  @return  Query
     */
    public function onGSDGetListQuery($model, $context)
    {
        if ($context != $this->_name)
        {
            return;
        }

        return $this->getListQuery($model);
    }

    /**
     *  Construct the query needed for the item selection modal in the backend.
     *
     *  @param   JModel  $model  The Things Model
     *
     *  @return  Query
     */
    protected function getListQuery($model)
    {
        $db = $this->db;

        $state = $model->getState();

        $columnID    = $this->getColumn('id');
        $columnTitle = $this->getColumn('title');
        $columnState = $this->getColumn('state');

        // Select records from the database
        $query = $db->getQuery(true)
            ->select(array(
                $db->quoteName('a.' . $columnID, 'id'),
                $db->quoteName('a.' . $columnTitle, 'title'),
                $db->quoteName('a.' . $columnState, 'state')
            ))
            ->from($db->quoteName('#__' . $this->getTable()) . 'AS a');

        // Filter by search in title.
        $search = $state->get('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where($columnID . ' = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where($db->quoteName('a.' . $columnTitle) . ' LIKE ' . $search);
            }
        }

        // Add the list ordering clause.
        $query->order($db->escape('a.' . $columnID) . ' ' . $db->escape('DESC'));

        return $query;
    }

    /**
     *  Event triggered to get item's title from the database
     *  Used in backend.
     *
     *  @param   integer  $id       The item's ID
     *  @param   string   $context  The plugin context
     *
     *  @return  string             The item's title
     */
    public function onGSDGetItemTitle($id, $context)
    {
        if ($context != $this->_name)
        {
            return;
        }

        if (!$item = $this->getItem($id))
        {
            return;
        }

        $titleColumn = $this->getColumn('title');
        return isset($item->$titleColumn) ? $item->$titleColumn : "";
    }
    
    /**
     *  The event triggered before the JSON markup be appended to the document.
     *
     *  @param   array  &$data   The JSON snippets to be appended to the document
     *
     *  @return  void
     */
    public function onGSDBeforeRender(&$data)
    {
        // Run on same component context only
        if (!$this->passContext())
        {
            $this->log("Invalid Context");
            return;
        }

        // Let's check if the plugin supports the current component's view.
        if (!$payload = $this->getPayload())
        {
            return;
        }

        // Now, let's see if we have valid snippets for the active page. If not abort.
        if (!$this->snippets = $this->getSnippets())
        {
            $this->log("No snippets found. View: " . $this->getView());
            return;
        }

        // Prepare snippets
        foreach ($this->snippets as $snippet)
        {
            // Here, the payload must be merged with the snippet data
            $jsonData = $this->preparePayload($snippet, $payload);

            // Create JSON
            $jsonClass = new GSDJSON($jsonData);
            $json = $jsonClass->generate();

            // Add json back to main data object
            $data[] = $json;
        }
    }

    /**
     *  Validate context to decide whether the plugin should run or not.
     *
     *  @return   bool
     */
    protected function passContext()
    {
        return GSDHelper::getComponentAlias() == $this->_name;
    }

    /**
     *  Get Item's ID
     *
     *  @return  string
     */
    protected function getThingID()
    {
        return $this->app->input->getInt($this->thingRequestIDName);
    }

    /**
     *  Returns an array with the available snippets for the current page
     *
     *  @return  array     The valid items array
     */
    protected function getSnippets()
    {
        if (!$thing = $this->getThingID())
        {
            $this->log('Invalid request ID');
            return;
        }

        // Get a db connection.
        $db = $this->db;

        // Select records from the json table
        $query = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__gsd'))
            ->where($db->quoteName('thing') . ' = '. $thing)
            ->where($db->quoteName('plugin') . ' = '. $db->quote($this->_name))
            ->where($db->quoteName('state') . ' = 1');

        $db->setQuery($query);

        // Load the results
        $items = $db->loadAssocList();

        // Prepare manually-generated snippets
        foreach ($items as $key => $item)
        {
            if (!isset($item['params']))
            {
                continue;
            }

            // Get flatten offeset
            $params = new Registry($item['params']);

            $contentType = $params->get('contenttype');
            $s = new Registry($params->offsetGet("$contentType"));
            $s->set('contentType', $contentType);
            $s->set('customcode', $params->get('customcode'));

            $items[$key] = $s;
        }

        // Prepare auto-generated snippets. Auto-mode must be enabled in the plugin's configuration page.
        if (!is_null($this->defaultContentType) && (bool) $this->params->get("automode", false))
        {
            $items[] = new Registry(
                array('contentType' => $this->defaultContentType)
            );
        }

        return $items;
    }

    /**
     *  Get Item's title
     *  Used in backend.
     *
     *  @param   integer  $id       The item's ID
     *  @param   string   $context  The plugin context
     *
     *  @return  string             The item's title
     */
    protected function getItem($id)
    {
        // Check if the result is already cached
        $hash = md5("getItem" . $this->_name . $id);
        if ($cache = Cache::get($hash))
        {
            return $cache;
        }

        // Get a db connection.
        $db = $this->db;

        // Select records from the json table
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__' . $this->getTable()))
            ->where($db->quoteName($this->column_id) . ' = '. $id);

        $db->setQuery($query);
         
        // Cache and return the result
        return Cache::set($hash, $db->loadObject());
    }

    /**
     *  Get plugin assosiated table name. If not available the extensions's name will be used instead.
     *
     *  @return  string
     */
    protected function getTable()
    {
        return isset($this->table) ? $this->table : $this->_name;
    }

    /**
     *  Get plugin assosiated table columns.
     *
     *  @param   string  $column_name  The column name
     *
     *  @return  string
     */
    protected function getColumn($column_name)
    {
        $property = 'column_' . $column_name;
        return isset($this->$property) ? $this->$property : '';
    }

    /**
     *  Asks for data from the child plugin based on the active view name
     *
     *  @return  Registry  The payload Registry
     */
    private function getPayload()
    {
        $view   = $this->getView();
        $method = 'view' . ucfirst($view);

        if (!$view || !method_exists($this, $method))
        {
            $this->log("View '$view' not supported");
            return;
        }

        // Yeah. Let's call the method. 
        $payload = $this->$method();

        // We need a valid array
        if (!is_array($payload))
        {  
            $this->log('Invalid Payload Array');
            return;
        }

        // Convert payload to Registry object and return it
        return new Registry($payload);
    }

    /**
     *  Prepares the payload to be used in the JSON class
     *
     *  @return  string
     */
    private function preparePayload($snippet, $payload)
    {
        // Image. Defaults to Inherit.
        switch ($snippet->get('image.option'))
        {
            case '2': // Custom uploaded image
                $image = $snippet->get('image.file');
                break;
            case '3': // Custom image URL
                $image = $snippet->get('image.url');
                break;
            default: // Inherit item's image
                $image = $payload["image"];
                break;
        }

        // Rating. Defaults to Inherit.
        switch ($snippet->get('rating.option'))
        {
            case '0': // Disabled
                $ratingValue = 0;
                $reviewCount = 0;
                break;
            case '2': // Custom rating
                $ratingValue = $snippet->get('rating.ratingValue');
                $reviewCount = $snippet->get('rating.reviewCount');
                break;
            default: // Inherit item's rating
                $ratingValue = $payload['ratingValue'];
                $reviewCount = $payload['reviewCount'];
                break;
        }

        // Author. Defaults to Inherit.
        switch ($snippet->get('author.option'))
        {
            case '1': // Select Joomla user
                $authorname = JFactory::getUser($snippet->get('author.user'))->name;
                break;
            case '2': // Set custom author name
                $authorname = $snippet->get('author.custom');
                break;
            default: // Inherit item's author
                $authorname = JFactory::getUser($payload["created_by"])->name;
                break;
        }

        // On form saving, the Calendar field is set to '0000-00-00 00:00:00' if there is no date selected by the user which causes
        // wrong result with the payload merge. To fix this issue, we need to unset the date values.
        if ($snippet->get('publish_up') == '0000-00-00 00:00:00')
        {
            $snippet->set('publish_up', null);
        }

        if ($snippet->get('modified') == '0000-00-00 00:00:00')
        {
            $snippet->set('modified', null);
        }

        // Create a new combined object by merging the snippet data into the payload
        // Note: In order to produce a valid merged object, payload's array keys should match the field names
        // as declared in the form's XML file.
        $p = clone $payload;
        $s = $p->merge($snippet, false);

        // Shorthand for the content type
        $contentType    = $snippet['contentType'];
        $prepareContent = GSDHelper::getParams()->get("preparecontent", false);
        $descCharsLimit = GSDHelper::getParams()->get("desclimit", 300);

        // Prepare common data
        $commonData = array(
            "contentType"   => $contentType,
            "title"         => GSDHelper::makeTextSafe($s['headline'], $prepareContent),
            "description"   => GSDHelper::makeTextSafe($s['description'], $prepareContent, $descCharsLimit),
            "image"         => GSDHelper::absURL($image),

            // Author / Publisher
            "authorName"    => $authorname,
            "publisherName" => GSDHelper::getSiteName(),
            "publisherLogo" => GSDHelper::getSiteLogo(),

            // Rating
            "ratingValue"   => $ratingValue,
            "reviewCount"   => $reviewCount,
            "bestRating"    => $s['bestRating'],
            "worstRating"   => $s['worstRating'],

            // Dates
            "datePublished" => GSDHelper::date($s["publish_up"]),
            "dateCreated"   => GSDHelper::date($s["created"]),
            "dateModified"  => GSDHelper::date($s["modified"]),

            // Site based
            "url"           => JURI::current(),
            "siteurl"       => GSDHelper::getSiteURL(),
            "sitename"      => GSDHelper::getSiteName(),
            "custom"        => $s['customcode']
        );

        // Prepare snippet data
        $data = array();
        switch ($contentType)
        {
            case 'product':
                $data = array(
                    "offerPrice"   => $s['offerPrice'],
                    "brand"        => $s['brand'],
                    "sku"          => $s['sku'],
                    "currency"     => $s['currency'],
                    "condition"    => $s['offerItemCondition'],
                    "availability" => $s['offerAvailability']
                );
                break;
            case 'event':
                $startDate          = $s['startDate'] . ' ' . $s['startTime'];
                $endDate            = $s['endDate'] . ' ' . $s['endTime'];
                $offerStartDateTime = $s['offerStartDate'] . ' ' . $s['offerStartTime'];
                
                $data = array(
                    "type"      => $s['type'],
                    "starttime" => $s['startTime'],
                    "startdate" => GSDHelper::date($startDate),
                    "enddate"   => GSDHelper::date($endDate),
                    "location"  => array("name" => $s['locationName'], "address" => $s['locationAddress']),
                    "performer" => array("name" => $s['performerName'], "type" => $s['performerType']),
                    "status"    => $s['status'],
                    "offer"     => array(
                        "availability"   => $s['offerAvailability'], 
                        "startDateTime"  => GSDHelper::date($offerStartDateTime),
                        "price"          => $s['offerPrice'],
                        "currency"       => $s['offerCurrency'],
                        "inventoryLevel" => $s['offerInventoryLevel']
                    )
                );
                break;
            case 'recipe':
                $data = array(
                    "prepTime"      => "PT" . $s['prepTime'] . "M",
                    "cookTime"      => "PT" . $s['cookTime'] . "M",
                    "totalTime"     => "PT" . $s['totalTime'] . "M",
                    "calories"      => $s['calories'],
                    "yield"         => $s['yield'],
                    "ingredient"    => $this->makeArrayFromNewLine($s['ingredient']),
                    "instructions"  => $this->makeArrayFromNewLine($s['instructions']),
                );
                break;
            case 'review':
                $data = array(
                    "itemReviewedType" => $s['itemReviewedType'],
                    "address"          => $s['address'],
                    "priceRange"       => $s['priceRange'],
                    "telephone"        => $s['telephone']
                );
                break;
            case 'factcheck':
                switch ($s['factcheckRating']) {
                    // there is no textual representation for zero (0)
                    case '1':
                        $textRating = 'False';
                        break;
                    case '2':
                        $textRating = 'Mostly false';
                        break;
                    case '3':
                        $textRating = 'Half true';
                        break;
                    case '4':
                        $textRating = 'Mostly true';
                        break;
                    case '5':
                        $textRating = 'True';
                        break;
                    default:
                        $textRating = 'Hard to categorize';
                        break;
                }

                $data = array(
                    "factcheckURL"          => ($s['multiple']) ? $commonData['url'] . $s['anchorName'] : $commonData['url'],
                    "claimAuthorType"       => $s['claimAuthorType'],
                    "claimAuthorName"       => $s['claimAuthorName'],
                    "claimURL"              => $s['claimURL'],
                    "claimDatePublished"    => $s['claimDatePublished'],
                    "factcheckRating"       => $s['factcheckRating'],
                    "bestFactcheckRating"   => ($s['factcheckRating'] != '-1') ? '5' : '-1',
                    "worstFactcheckRating"  => ($s['factcheckRating'] != '-1') ? '1' : '-1',
                    "alternateName"         => $textRating
                );
                break;
            case 'video':
                $data = array(
                    "contentUrl" => $s['contentUrl']
                );
                break;
        }

        return array_merge($data, $commonData);
    }

    /**
     *  Get View Name
     *
     *  @return  string  Return the current executed view in the front-end
     */
    protected function getView()
    {
        return $this->app->input->get($this->thingRequestViewVar);
    }

    /**
     *  Log messages
     *
     *  @param   string  $message  The message to log
     *
     *  @return  void
     */
    protected function log($message)
    {
        GSDHelper::log(ucfirst($this->_name) . ' - ' . $message);
    }

    /**
     *  MOVE TO HELPER
     *  
     *  Split string into array on each new line character
     *
     *  @param   string  $str  The string to split
     *
     *  @return  array
     */
    private function makeArrayFromNewLine($str)
    {
        $array = preg_split("/\\r\\n|\\r|\\n/", $str);

        if (!$array)
        {
            return $str;
        }

        return array_values(array_filter($array));
    }
}

?>