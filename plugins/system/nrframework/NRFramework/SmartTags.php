<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die('Restricted access');

use \NRFramework\WebClient;

/**
 *   SmartTags replaces placeholder variables in a string
 */
class SmartTags
{
	/**
	 *  Joomla Document Object
	 *
	 *  @var  object
	 */
	private $doc;

	/**
	 *  Joomla User Object
	 *
	 *  @var  object
	 */
	private $user;

	/**
	 *  Tags Array
	 *
	 *  @var  array
	 */
	private $tags = array();

	/**
	 *  Tag placeholder
	 *
	 *  @var  string
	 */
	private $placeholder = "{}";

	/**
	 *  Class constructor
	 */
	function __construct($options = array())
	{
		$user = isset($options['user']) ? $options['user'] : null;

		$this->user = \JFactory::getUser($user);
		$this->setUserFirstLastName();

		$this->doc  = \JFactory::getDocument();

		$this->tags = array(
			// Server
			'url'			=> \JURI::getInstance()->toString(),
			'url.encoded'	=> urlencode(\JURI::getInstance()->toString()),
			'url.path'		=> \JURI::current(),
			'referrer'	    => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
			'ip'			=> isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,

			// Site 
			'site.name'     => \JFactory::getConfig()->get('sitename'),
			'site.url'      => \JURI::root(),

			// Page
			'page.title'    => $this->doc->getTitle(),
			'page.desc'     => $this->doc->getDescription(),
			'page.lang'     => $this->doc->getLanguage(),

			// User
			'user.id'        => $this->user->id,
			'user.name'      => $this->user->name,
			'user.firstname' => $this->user->firstname,
			'user.lastname'  => $this->user->lastname,
			'user.login'     => $this->user->username,
			'user.email'     => $this->user->email,
			'user.groups'    => implode(",", $this->user->groups),

			// Client
			'client.device'    => WebClient::getDeviceType(),
			'client.os'        => WebClient::getOS(),
			'client.browser'   => WebClient::getBrowser()['name'],
			'client.useragent' => WebClient::getClient()->userAgent,
			
			// Other
			'date'			=> \JFactory::getDate()->format('Y-m-d H:i:s'),
			'randomid'		=> bin2hex(\JCrypt::genRandomBytes(8))
		);
	}

	/**
	 *  Returns list of all tags
	 *
	 *  @return  array
	 */
	public function get($prepare = true)
	{
		if ($prepare)
		{
			$this->prepare();
		}

		return $this->tags;
	}

	/**
	 *  Sets the tag placeholder
	 *  For example: {} or [] or {{}} or {[]}
	 *
	 *  @param  string  $placeholder  
	 */
	public function setPlaceholder($placeholder)
	{
		$this->placeholder = $placeholder;
		return $this;
	}

	/**
	 *  Returns placeholder in 2 pieces
	 *
	 *  @return  array
	 */
	private function getPlaceholder()
	{
		return str_split($this->placeholder, strlen($this->placeholder) / 2);
	}

	/**
	 *  Adds Custom Tags to list
	 *
	 *  @param  array  $tags  Tags array in key value pairs
	 */
	
	/**
	 *  Adds Custom Tags to the list
	 *
	 *  @param  Mixed   $tags    Tags list (Array or Object)
	 *  @param  String  $prefix  A string to prefix all keys
	 */
	public function add($tags, $prefix = null)
	{
		// Convert Object to array
		if (is_object($tags))
		{
			$tags = (array) $tags;
		}

		if (!is_array($tags) || !count($tags))
		{
			return;
		}

		// Add Prefix to keys
		if ($prefix)
		{
			foreach ($tags as $key => $value)
			{
		        $newKey = $prefix . $key;
		        $tags[$newKey] = $value;
		        unset($tags[$key]);
			}
		}

		$this->tags = array_merge($this->tags, $tags);

		return $this;
	}

    /**
     *  Replace tags in object
     *
     *  @param   mixed  $obj  The data object for search for smarttags
     *
     *  @return  mixed
     */
    public function replace($obj)
    {
    	$this->prepare();

    	// Convert object to array
    	$data = is_object($obj) ? (array) $obj : $obj;

    	// Array case
    	if (is_array($data))
    	{
    		foreach ($data as $key => $value)
    		{
    			if (is_array($value) || is_object($value))
    			{
    				continue;
    			}

    			$data[$key] = strtr($value, $this->tags);
    		}

			// Revert object back to its original state
			$data = is_object($obj) ? (object) $data : $data;

	   		return $data;
    	}

    	// String case
    	return strtr($data, $this->tags);
    }

    /**
     *  Prepares tags by adding the placeholder to each key
     *
     *  @return  void
     */
    private function prepare()
    {
    	$placeholder = $this->getPlaceholder();

    	foreach ($this->tags as $key => $variable)
    	{
    		// Check if tag is already prepared
    		if (substr($key, 0, 1) == $placeholder[0])
			{
				continue;
			}

			// If the object passed to $replace method is in JSON format
			// we need to escape double quotes in the tag value to prevent JSON failure
			$this->tags[$placeholder[0] . $key . $placeholder[1]] = addcslashes($variable, '"');

			unset($this->tags[$key]);
    	}
    }

    /**
     *  Split User's name into First and Last name
     *
     *  @return  void
     */
    private function setUserFirstLastName()
    {
    	$this->user->name      = ucwords(strtolower($this->user->name));

    	$nameParts = explode(' ', $this->user->name, 2);

    	$this->user->firstname = trim($nameParts[0]);
    	$this->user->lastname  = isset($nameParts[1]) ? trim($nameParts[1]) : $this->user->firstname;
    }
}

?>