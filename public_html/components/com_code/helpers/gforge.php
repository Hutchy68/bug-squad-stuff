<?php
/**
 * @version		$Id: gforge.php 455 2010-10-07 17:51:44Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Connector class to a GForge Advanced Server SOAP API.
 *
 * @see      http://joomlacode.org/gf/xmlcompatibility/soap5/
 *
 * @package  Joomla.Code
 * @since    1.0
 */
class GForge
{
	/**
	 * @var    SoapClient  The client object connected to the GForge instance.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    string  The session hash for the SOAP session.
	 * @since  1.0
	 */
	protected $sessionhash;

	/**
	 * @var    string  The username for the signed in session.
	 * @since  1.0
	 */
	protected $username;

	/**
	 * Object constructor.  Creates the connection to the GForge site instance.
	 *
	 * @param   string  $site     The URL to the gforge instance.
	 * @param   array   $options  The SOAP options for the connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __construct($site, $options = array())
	{
		// Attempt to connect to the SOAP gateway.
		$this->client = new SoapClient($site.'/xmlcompatibility/soap5/?wsdl', $options);

		// Check for an error.
		if (!$this->client) {
			exit('Unable to connect to GForge instance at '.$site);
		}
	}

	/**
	 * Object destructor.  Signs out and closes the connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function __destruct()
	{
		// Check to see if the connection is live.
		if ($this->client) {

			// Check to see if we are signed in.
			if ($this->sessionhash) {
				$this->logout();
			}

			// Kill the connection.
			unset($this->client);
		}
	}

	/**
	 * Method to sign into GForge using password authentication.
	 *
	 * @param   string   $username  The username for the account to login.
	 * @param   string   $password  The password for the account to login.
	 *
	 * @return	boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function login($username, $password)
	{
		try {
			// Attempt to sign into the account and get the session hash.
			$sessionhash = $this->client->login($username, $password);

			// Cache the session hash and username for later use.
			$this->sessionhash = $sessionhash;
			$this->username = $username;

			return true;
		}
		catch (SoapFault $e)
		{
			die ('Login Failed: '.$e->faultstring);
			return false;
		}
	}

	/**
	 * Method to sign out of GForge.
	 *
	 * @return	boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function logout()
	{
		try {
			// Attempt to sign out.
			$this->client->logout($this->sessionhash);
			$this->sessionhash = null;
			$this->username = null;

			return true;
		}
		catch (SoapFault $e)
		{
			echo 'Logout Failed: '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get user data by username.
	 *
	 * @param   string  $username  The optional username to get user data for, defaults to the user
	 *                             signed into the current session.
	 *
	 * @return  mixed   Boolean false on failure, user data object on success.
	 *
	 * @since   1.0
	 */
	public function getUser($username = null)
	{
		try {
			// Attempt to get the user object by the username or "unix name" in GForge speak.
			$user = $this->client->getUserByUnixName($this->sessionhash, $username ? $username : $this->username);

			return $user;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get user '.($username ? $username : $this->username).': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get user data by username.
	 *
	 * @param   string  $username  The optional username to get user data for, defaults to the user
	 *                             signed into the current session.
	 *
	 * @return  mixed   Boolean false on failure, user data object on success.
	 *
	 * @since   1.0
	 */
	public function getUsersById($ids = array())
	{
		try {
			// Attempt to get the user object by the username or "unix name" in GForge speak.
			$users = $this->client->getUserArray($this->sessionhash, $ids);

			return $users;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get users ('.implode(',', $ids).'): '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get the projects a user belongs to by username.
	 *
	 * @param   string  $username  The optional username to get the project list for, defaults to
	 *                             the user signed into the current session.
	 *
	 * @return  mixed   Boolean false on failure, project data array on success.
	 *
	 * @since   1.0
	 */
	public function getUserProjects($username = null)
	{
		try {
			// Attempt to get the project data array by the username or "unix name" in GForge speak.
			$projects = $this->client->getUserProjects($this->sessionhash, $username ? $username : $this->username);

			return $projects;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get projects for user '.($username ? $username : $this->username).': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a project object by name.
	 *
	 * @param   string  $name  The name of the project for which to get the data object.
	 *
	 * @return  mixed   Boolean false on failure, project data object on success.
	 *
	 * @since   1.0
	 */
	public function getProject($name)
	{
		try {
			// Attempt to get the project data object by the name or "unix name" in GForge speak.
			$project = $this->client->getProjectByUnixName($this->sessionhash, $name);

			return $project;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get project '.$name.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get the project trackers by project name or id.
	 *
	 * @param   mixed  $project  Either the project name or numeric id for the project to get a list
	 *                           of tracker data objects.
	 *
	 * @return  mixed  Boolean false on failure, tracker data array on success.
	 *
	 * @since   1.0
	 */
	public function getProjectTrackers($project)
	{
		// If a project name was given go find the project id based on the name.
		if (!is_numeric($project)) {
			// Attempt to get the project object from the name.
			$project = $this->getProject($project);

			// Assign the project id based on the returned project or return false if not found.
			if ($project) {
				$projectId = $project->project_id;
			}
			else {
				return false;
			}
		}
		// Easy peasy...
		else {
			$projectId = $project;
		}

		try {
			// Attempt to get the project tracker array by the project id.
			$trackers = $this->client->getTrackers($this->sessionhash, $projectId, true, -1);

			return $trackers;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get trackers for project '.$project.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a tracker object by id.
	 *
	 * @param   int     $trackerId  The tracker id for which to get the data object.
	 *
	 * @return  mixed   Boolean false on failure, tracker data object on success.
	 *
	 * @since   1.0
	 */
	public function getTracker($trackerId)
	{
		try {
			// Attempt to get the tracker data object by id.
			$tracker = $this->client->getTracker($this->sessionhash, $trackerId);

			return $tracker;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get tracker '.$trackerId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of tracker fields from a specific tracker by tracker id.
	 *
	 * @param   int    $trackerId  The numeric id of the tracker for which to get a list of fields.
	 *
	 * @return  mixed  Boolean false on failure, tracker field data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerFields($trackerId)
	{
		try {
			// Attempt to get a list of tracker field data by tracker id.
			$fields = $this->client->getTrackerExtraFields($this->sessionhash, $trackerId, -1);

			return $fields;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get fields for tracker '.$trackerId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of tracker field values from a specific field by field id.
	 *
	 * @param   int    $fieldId  The numeric id of the field for which to get a list of values.
	 *
	 * @return  mixed  Boolean false on failure, tracker field value data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerFieldValues($fieldId)
	{
		try {
			// Attempt to get a list of tracker field values by field id.
			$fields = $this->client->getTrackerExtraFieldElements($this->sessionhash, $fieldId, '', -1, -1);

			return $fields;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get values for tracker field '.$fieldId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of tracker items from a specific tracker by tracker id.
	 *
	 * @param   int    $trackerId  The numeric id of the tracker for which to get a list of items.
	 *
	 * @return  mixed  Boolean false on failure, tracker item data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItems($trackerId)
	{
		try {
			// Attempt to get a list of tracker item data by tracker id.
			// Get in batches of 5000 to avoid errors
			$items = $this->client->getTrackerItemsShort($this->sessionhash, $trackerId, -1, -1, 3000, 0);
			$items1 = $this->client->getTrackerItemsShort($this->sessionhash, $trackerId, -1, -1, 5000, 3000);
			$items = array_merge($items, $items1);
			foreach ($items as $item) {
				$itemArray[$item->tracker_item_id] = true;
			}
// 			krsort($itemArray);
			return $items;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get tracker items for tracker '.$trackerId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a tracker item object by id.
	 *
	 * @param   int     $itemId  The tracker item id for which to get the data object.
	 *
	 * @return  mixed   Boolean false on failure, tracker item data object on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItem($itemId)
	{
		try {
			// Attempt to get the item data object by item id.
			$item = $this->client->getTrackerItemFull($this->sessionhash, $itemId);

			return $item;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get tracker item '.$itemId.': '.$e->faultstring."\n";
			debug_print_backtrace();
			return false;
		}
	}

	/**
	 * Method to get an array of tracker item changes by id.
	 *
	 * @param   int     $itemId  The tracker item id for which to get the changes array.
	 *
	 * @return  mixed   Boolean false on failure, tracker item changes data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItemChanges($itemId)
	{
		try {
			// Attempt to get the changes data array by the tracker item id.
			$changes = $this->client->getAuditTrails($this->sessionhash, $itemId);

			return $changes;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get changes for tracker item '.$itemId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get an array of tracker item messages by id.
	 *
	 * @param   int     $itemId  The tracker item id for which to get the messages array.
	 *
	 * @return  mixed   Boolean false on failure, tracker item messages data array on success.
	 *
	 * @since   1.0
	 */
	public function getTrackerItemMessages($itemId)
	{
		try {
			// Attempt to get the messages data array by the tracker item id.
			$messages = $this->client->getTrackerItemMessages($this->sessionhash, $itemId, -1);

			return $messages;
		}
		catch (SoapFault $e)
		{
			echo 'Unable to get messages for tracker item '.$itemId.': '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of client functions.
	 *
	 * @return  mixed   Boolean false on failure, array of functions on success.
	 *
	 * @since   1.0
	 */
	protected function getClientFunctions()
	{
		try {
			// Attempt to get the client functions.
			$functions = $this->client->__getFunctions();

			return $functions;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get functions: '.$e->faultstring."\n";
			return false;
		}
	}

	/**
	 * Method to get a list of client types.
	 *
	 * @return  mixed   Boolean false on failure, array of types on success.
	 *
	 * @since   1.0
	 */
	protected function getClientTypes()
	{
		try {
			// Attempt to get the client types.
			$functions = $this->client->__getTypes();

			return $functions;
		}
		catch (SoapFault $e)
		{
			echo 'Failed to get types: '.$e->faultstring."\n";
			return false;
		}
	}
}