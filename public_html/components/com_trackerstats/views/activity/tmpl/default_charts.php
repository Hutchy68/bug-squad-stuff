<?php
/**
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for joomaprosubs
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on trackerstats permissions.
$canEdit = $user->authorise('core.edit', 'com_trackerstats');

$listOrder	= '';
$listDirn	= '';
$listFilter = '';
// $jsonSource = $this->baseurl . "/components/com_trackerstats/json/getbarchartdata.php";
$jsonSource = $this->baseurl . '/index.php?option=com_trackerstats&task=activity.display&format=json';
JHtml::_('barchart.barchart', 'barchart', 'barchart', false);
?>

<h2>Bug Squad Activity</h2>
<div id="barchart" style="width:600px; height:300px;" href="<?php echo $jsonSource; ?>"></div>
</br>
<a href="<?php echo $jsonSource; ?>">See Data</a>
<fieldset class="filters btn-toolbar">
<h3>Chart Options</h3>
	<div class="btn-group pull-right">
		<label for="period" class="element-invisible">Period</label>
		<select id="period" name="period" class="inputbox input-mini" size="1" >
			<option value="1" selected="selected">7 Days</option>
			<option value="2">30 Days&nbsp;&nbsp;</option>
			<option value="3">90 Days&nbsp;&nbsp;</option>
		</select>
		<label for="type" class="element-invisible">&nbsp;&nbsp;Type</label>
		<select id="type" name="type" class="inputbox input-mini" size="1" >
			<option value="0" >All</option>
			<option value="1" selected="selected">Tracker&nbsp;&nbsp;</option>
			<option value="2">Test</option>
			<option value="3">Code</option>
		</select>
		&nbsp;&nbsp;<button class="dataUpdate" id="dataUpdate" >&nbsp;&nbsp;Update Chart&nbsp;&nbsp;</button>
	</div>
  </fieldset>