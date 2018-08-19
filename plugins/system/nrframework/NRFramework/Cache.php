<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die;

/**
 *  Caching mechanism
 */
class Cache
{

	/**
	 *  Static cache object
	 *
	 *  @var  array
	 */
	static $cache = array();

	/**
	 *  Check if has alrady exists in memory
	 *
	 *  @param   string   $hash  The hash string
	 *
	 *  @return  boolean         
	 */
	static public function has($hash)
	{
		return isset(self::$cache[$hash]);
	}

	/**
	 *  Returns hash valuw
	 *
	 *  @param   string  $hash  The hash string
	 *
	 *  @return  mixed          False on error, Object on success
	 */
	static public function get($hash)
	{
		if (!self::has($hash))
		{
			return false;
		}

		return is_object(self::$cache[$hash]) ? clone self::$cache[$hash] : self::$cache[$hash];
	}

	/**
	 *  Sets on memory the hash value
	 *
	 *  @param  string  $hash  The hash string
	 *  @param  mixed   $data  Can be string or object
	 *
	 *  @return mixed
	 */
	static public function set($hash, $data)
	{
		self::$cache[$hash] = $data;
		return $data;
	}

	/**
	 *  Reads hash valuw from memory or file
	 *
	 *  @param   string   $hash   The hash string
	 *  @param   boolean  $force  If true, the filesystem will be used as well on the /cache/ folder
	 *
	 *  @return  mixed            The hash object valuw
	 */
	static public function read($hash, $force = false)
	{
		if (self::has($hash))
		{
			return self::get($hash);
		}

		$cache = \JFactory::getCache('novarain', '');

		if ($force)
		{
			$cache->setCaching(true);
		}

		return $cache->get($hash);
	}

	/**
	 *  Writes hash value in cache folder
	 *
	 *  @param   string   $hash  The hash string
	 *  @param   mixed    $data  Can be string or object
	 *  @param   integer  $ttl   Expiration duration in milliseconds
	 *
	 *  @return  mixed           The hash object value
	 */
	static public function write($hash, $data, $ttl = 0)
	{
		$cache = \JFactory::getCache('novarain','');

		if ($ttl)
		{
			$cache->setLifeTime($ttl * 60);
		}

		$cache->setCaching(true);
		$cache->store($data, $hash);

		self::set($hash, $data);

		return $data;
	}
}
