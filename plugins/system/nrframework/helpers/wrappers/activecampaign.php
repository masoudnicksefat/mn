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

class NR_ActiveCampaign extends NR_Wrapper
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
		$this->setEndpoint($options['endpoint']);
		$this->options->set('headers.Content-Type', 'application/x-www-form-urlencoded');
		$this->options->set('follow_location', true);
	}

	/**
	 *  Subscribe user to ActiveCampaign List
	 *
	 *  http://www.activecampaign.com/api/example.php?call=contact_sync
	 *
	 *  @param   string  $email           The name of the Contact
	 *  @param   string  $name            Email of the Contact
	 *  @param   string  $list            List ID
	 *  @param   string  $tags            Tags for this contact (comma-separated). Example: "tag1, tag2, etc"
	 *  @param   array   $customfields    Custom Fields
	 *  @param   boolean $updateexisting  Update Existing User
	 *
	 *  @return  void                   
	 */
	public function subscribe($email, $name, $list, $tags = "", $customfields = array(), $updateexisting)
	{
		$name = explode(" ", $name, 2);

		$customFields = $this->validateCustomFields($customfields);

		$apiAction = ($updateexisting) ? 'contact_sync' : 'contact_add';

		$data = array(
			"api_action"           => $apiAction,
			"email"                => $email,
			"first_name"           => isset($name[0]) ? $name[0] : null,
			"last_name"            => isset($name[1]) ? $name[1] : null,
			"p[" . $list . "]"     => $list,
			"tags"                 => $tags,
			"status[1]"            => 1,
			"instantresponders[1]" => 1,
			"ip4"                  => $_SERVER['REMOTE_ADDR']
		);

		$data = array_merge($data, $customFields);

		$this->post('', $data);
	}

	/**
	 *  Returns all available lists
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$data = $this->get('', array('api_action' => 'list_list', 'ids' => 'all'));
		$lists = array();

		if (isset($data['result_code']) && $data['result_code'] == 0)
		{
			return $lists;
		}

		unset($data['result_code'], $data['result_message'], $data['result_output']);

		foreach ($data as $list)
		{
			$lists[] = array(
				"id"   => $list["id"],
				"name" => $list["name"]
			);
		}

		return $lists;
	}

	/**
	 *  Returns the Active Campaign Account's Custom Fields
	 *
	 *  API Reference
	 *  http://www.activecampaign.com/api/example.php?call=list_field_view
	 *
	 *  @return  array
	 */
	public function getCustomFields()
	{
		$data = $this->get('', array('api_action' => 'list_field_view', 'ids' => 'all'));

		if (isset($data['result_code']) && $data['result_code'] == 0)
		{
			return array();
		}

		unset($data['result_code'], $data['result_message'], $data['result_output']);

		return $data;
	}

	/**
	 *  Returns a new array with valid only custom fields
	 *
	 *  @param   array  $formCustomFields   Array of custom fields
	 *
	 *  @return  array  					Array of valid only custom fields
	 */
	public function validateCustomFields($formCustomFields)
	{
		$fields = array();

		if (!is_array($formCustomFields))
		{
			return $fields;
		}

		$listCustomFields = $this->getCustomFields();

		if (!$this->request_successful)
		{
			return $fields;
		}

		$formCustomFieldsKeys = array_change_key_case(array_keys($formCustomFields), CASE_LOWER);

		foreach ($listCustomFields as $listCustomField)
		{
			
			if (!in_array(strtolower($listCustomField['title']), $formCustomFieldsKeys))
			{
				continue;
			}

			$fields['field[' . $listCustomField['id'] . ',0]'] = $formCustomFields[strtolower($listCustomField['title'])];
		}

		return $fields;
	}

	/**
	 * Check if the response was successful or a failure. If it failed, store the error.
	 *
	 * @return bool     If the request was successful
	 */
	protected function determineSuccess()
	{
		$serviceStatus = $this->findHTTPStatus();

		// Find Active Campaign true application status
		$body              = $this->last_response['body'];
		$applicationStatus = (bool) isset($body['result_code']) ? $body['result_code'] : false;

		if (($serviceStatus >= 200 && $serviceStatus <= 299) && $applicationStatus)
		{
			return ($this->request_successful = true);
		}

		// Request Failed - Set the last error
		$this->last_error = isset($body["result_message"]) ? $body["result_message"] : "";
	}

	/**
	 *  Setter method for the endpoint
	 *
	 *  @param  string  $url  The URL which is set in the account's developer settings
	 */
	public function setEndpoint($url)
	{
		if (!empty($url))
		{
			$query          = http_build_query(array('api_key' => $this->key, 'api_output' => 'json'));
			$this->endpoint = $url . '/admin/api.php?' . $query;
		}
		else
		{
			throw new \Exception("Invalid ActiveCampaign URL `{$url}` supplied.");
		}
	}

	/**
	 * Encode the data and attach it to the request
	 * @param   array $data Assoc array of data to attach
	 */
	protected function attachRequestPayload($data)
	{
		$this->last_request['body'] = http_build_query($data);
	}
}
