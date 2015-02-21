<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_code
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Issue Model for Joomla Code
 */
class CodeModelIssue extends JModelLegacy
{
	public function getItem($issueId = null)
	{
		$issueId = empty($issueId) ? JFactory::getApplication()->input->getInt('issue_id') : $issueId;

		$db = $this->getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__code_tracker_issues'))
				->where($db->quoteName('issue_id') . ' = ' . (int) $issueId)
		);

		try
		{
			return $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, 'Unable to access resource: ' . $e->getMessage());
		}
	}

	public function getTags($issueId = null)
	{
		$issueId = empty($issueId) ? JFactory::getApplication()->input->getInt('issue_id') : $issueId;

		$db = $this->getDbo();

		$db->setQuery(
		   $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__code_tracker_issue_tag_map'))
				->where($db->quoteName('issue_id') . ' = ' . (int) $issueId)
				->order('tag ASC')
		);

		try
		{
			return $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseError(500, 'Unable to access resource: ' . $e->getMessage());
		}
	}
}
