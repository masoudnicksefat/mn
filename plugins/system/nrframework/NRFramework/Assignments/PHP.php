<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/
namespace NRFramework\Assignments;

defined('_JEXEC') or die;

use NRFramework\Assignment;

class PHP extends Assignment 
{
	/**
	 *  Pass check Custom PHP
	 *
	 *  @return  bool
	 */
	function passPHP()
	{
		if (!is_array($this->selection))
		{
			$this->selection = array($this->selection);
		}

		$pass = false;
		foreach ($this->selection as $php)
		{
			// replace \n with newline and other fix stuff
			$php = str_replace('\|', '|', $php);
			$php = preg_replace('#(?<!\\\)\\\n#', "\n", $php);
			$php = trim(str_replace('[:REGEX_ENTER:]', '\n', $php));

			if ($php == '')
			{
				$pass = true;
				break;
			}

			if (!isset($Itemid))
			{
				$Itemid = $this->request->Itemid;
			}

			if (!isset($mainframe))
			{
				$mainframe = $this->app;
			}

			if (!isset($app))
			{
				$app = $this->app;
			}

			if (!isset($document))
			{
				$document = $this->doc;
			}

			if (!isset($doc))
			{
				$doc = $this->doc;
			}

			if (!isset($database))
			{
				$database = $this->db;
			}

			if (!isset($db))
			{
				$db = $this->db;
			}

			if (!isset($user))
			{
				$user = $this->user;
			}

			$php .= ';return true;';

			$temp_PHP_func = create_function('&$Itemid, &$mainframe, &$app, &$document, &$doc, &$database, &$db, &$user', $php);

			// evaluate the script
			ob_start();
			$pass = (bool) $temp_PHP_func($Itemid, $mainframe, $app, $document, $doc, $database, $db, $user);
			unset($temp_PHP_func);
			ob_end_clean();

			if ($pass)
			{
				break;
			}
		}

		return $pass;
	}
}
