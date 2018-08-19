<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access
defined('_JEXEC') or die;

require_once __DIR__ . '/wrapper.php';

class NR_CampaignMonitor extends NR_Wrapper
{

	/**
	 * Create a new instance
	 * @param array $options The service's required options
	 * @throws \Exception
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options['api']);
		$this->setEndpoint('https://api.createsend.com/api/v3.1');
		$this->options->set('userauth', $this->key);
		$this->options->set('passwordauth', 'nopass');
	}

	/**
	 *  Set the API Key
	 *
	 *  @param  string
	 */
	public function setKey($key)
	{
		if (!empty($key))
		{
			$this->key = $key;
		}
		else
		{
			throw new \Exception("Invalid Campaign Monitor key `{$key}` supplied.");
		}
	}

	/**
	 *  Subscribe user to Campaign Monitor
	 *
	 *  API References:
	 *  https://www.campaignmonitor.com/api/subscribers/#importing_many_subscribers
	 *  Reminder:
	 *  The classic add_subscriber method of Campaign Monitor's API is NOT instantaneous!
	 *  It is suggested to use their import method for instantaneous subscriptions!
	 *
	 *  @param   string   $email         	  User's email address
	 *  @param 	 string   $name 			  User's Name
	 *  @param   string   $list          	  The Campaign Monitor list unique ID
	 *  @param   array    $custom_fields  	  Custom Fields
	 *
	 *  @return  void
	 */
	public function subscribe($email, $name, $list, $customFields = array())
	{
		$data = array(
			'Subscribers' => array(
				array(
					'EmailAddress' => $email,
					'Name'         => $name,
					'Resubscribe'  => true,
				),
			),
		);

		if (is_array($customFields) && count($customFields))
		{
			$data['Subscribers'][0]['CustomFields'] = $this->validateCustomFields($customFields, $list);
		}

		$this->post('subscribers/' . $list . '/import.json', $data);

		return true;
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  @param   array  $formCustomFields   Array of custom fields
	 *
	 *  @return  array  					Array of valid only custom fields
	 */
	public function validateCustomFields($formCustomFields, $list)
	{
		$fields = array();

		if (!is_array($formCustomFields))
		{
			return $fields;
		}

		$listCustomFields = $this->get('lists/' . $list . '/customfields.json');

		if (!$this->request_successful)
		{
			return $fields;
		}

		$formCustomFieldsKeys = array_keys($formCustomFields);

		foreach ($listCustomFields as $listCustomField)
		{
			if (!in_array($listCustomField['FieldName'], $formCustomFieldsKeys))
			{
				continue;
			}

			$fields[] = array(
				"Key"   => $listCustomField['FieldName'],
				"Value" => $formCustomFields[$listCustomField['FieldName']],
			);
		}

		return $fields;
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body = $this->last_response['body'];

		$message = '';

		if (isset($body['Code']))
		{
			$message = $body['Message'];
		}

		if (isset($body['ResultData']['FailureDetails']['Message']))
		{
			$message .= ' - ' . $body['ResultData']['FailureDetails']['Message'];
		}

		return $message;
	}

	/**
	 *  Returns all Client lists
	 *
	 *  https://www.campaignmonitor.com/api/clients/#getting-subscriber-lists
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$clients = $this->getClients();

		if (!is_array($clients))
		{
			return;
		}

		$lists = array();

		foreach ($clients as $key => $client)
		{
			if (!isset($client["ClientID"]))
			{
				continue;
			}

			$clientLists = $this->get("/clients/".$client["ClientID"]."/lists.json");

			if (!is_array($clientLists))
			{
				continue;
			}

			foreach ($clientLists as $key => $clientList)
			{
				$lists[] = array(
					"id"   => $clientList["ListID"],
					"name" => $clientList["Name"]
				);
			}
		}

		return $lists;
	}

	/**
	 *  Get Clients
	 *
	 *  https://www.campaignmonitor.com/api/account/
	 *
	 *  @return  mixed   Array on success, Exception on fail
	 */
	private function getClients()
	{
		$clients = $this->get("/clients.json");

		if (!$this->success())
		{
			throw new Exception($this->getLastError());
		}

		return $clients;
	}
}
