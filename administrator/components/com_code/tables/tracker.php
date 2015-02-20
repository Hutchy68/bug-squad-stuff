<?php
/**
 * @version		$Id: tracker.php 417 2010-06-25 01:01:45Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

/**
 * Code tracker table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableTracker extends JTable
{
	/**
	 * @var int Primary key
	 */
	public $tracker_id;

	/**
	 * @var	int	Foreign key to #__users.id
	 */
	public $project_id;

	/**
	 * @var	string	The URI path to the branch.
	 */
	public $title;

	/**
	 * @var	string	The name of the branch.
	 */
	public $alias;

	/**
	 * @var	string	A description of the branch purpose.
	 */
	public $summary;

	/**
	 * @var	int	The publishing state of the branch.
	 */
	public $description;

	/**
	 * @var	string	The date/time when the branch was last updated.
	 */
	public $state;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $item_count;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $open_item_count;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $created_date;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $created_by;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $modified_date;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $modified_by;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_tracker_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_project_id;

	/**
	 * Class constructor.
	 *
	 * @param	JDatabaseDriver  $db  A database connector object.
	 *
	 * @since	1.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__code_trackers', 'tracker_id', $db);
	}

	/**
	 * Method to load a data object by its legacy ID
	 *
	 * @param   integer  $legacyId  The tracker ID to load
	 *
	 * @return  boolean  True on success
	 */
	public function loadByLegacyId($legacyId)
	{
		// Load the database object
		$db = $this->getDbo();

		// Look up the tracker ID based on the legacy ID.
		$db->setQuery(
			$db->getQuery(true)
				->select($this->_tbl_key)
				->from($this->_tbl)
				->where('jc_tracker_id = ' . (int) $legacyId)
		);

		$issueId = (int) $db->loadResult();

		if ($issueId)
		{
			return $this->load($issueId);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Overrides JTable::store
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	public function store($updateNulls = false)
	{
		// Verify that a project ID is set
		if ($this->project_id === null)
		{
			// Get the project ID from the projects table if the jc_project_id is set
			if ($this->jc_project_id !== null)
			{
				$db = $this->getDbo();

				$db->setQuery(
					$db->getQuery(true)
						->select($db->quoteName('project_id'))
						->from($db->quoteName('#__code_projects'))
						->where($db->quoteName('jc_project_id') . ' = ' . $this->jc_project_id)
				);

				if ($result = $db->loadResult())
				{
					$this->project_id = (int) $result;
				}
			}
		}

		// Finish processing
		return parent::store($updateNulls);
	}
}
