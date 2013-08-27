<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$extension_data = $displayData['extensions'][0];
print_r($extension_data);
?>
<div class="item-view">
	<div class="grid-header">
		<div class="breadcrumbs">
			<a href="#">EXTENSIONS</a> / <a href="#">ADMIN NAVIGATION</a> / <span class="active-extension">B2JCONTACT</span>
		</div>
	</div>
	<div class="full-item-container">
		<img class="item-logo" src="<?php echo $extension_data->image; ?>" />
		<div class="item-info-container">
			<div class="item-title"><?php echo $extension_data->name; ?></div>
			<div>
				<ul class="item-type">
					<li title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="m">M</li>
					<li title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="p">P</li>
					<li title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="s">S</li>
					<li title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="t">T</li>
					<li title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="c">C</li>
				</ul>
			</div>
		
			<div class="rating">
				<i class="icon-star rated"></i>
				<i class="icon-star rated"></i>
				<i class="icon-star"></i>
				<i class="icon-star"></i>
				<i class="icon-star"></i>
			</div>
			<div class="item-version">
				Version 1.1 (updated 13 July 2013)
			</div>
		</div>
		<div style="clear:both;"></div>
		<div class="item-buttons">
			<a href=""><?php echo JText::_('COM_APPS_DIRECTORY_LISTING'); ?></a>
			<a href=""><?php echo JText::_('COM_APPS_DEVELOPER_WEBSITE'); ?></a>
			<a class="install" href=""><?php echo JText::_('COM_APPS_INSTALL'); ?></a>
		</div>
		<div class="item-desc">
			<p class="item-desc-title">
				Provide automatic keepalive for certain groups, and session timeout notifications for everyone else.
			</p>
			<p class="item-desc-text">
				Everyone knows how frustrating it is to be logged in, working on something important, only to learn that your session expired while you were working and all of your changes were lost.  Session Keeper resolves that issue by allowing an administrator to specify which groups are to be kept alive automatically.   Unlike other extensions that perform strange tests to figure out who is an admin, use wacky keepalive methods, or require you to enter a comma separated list of group ID numbers, this plugin allows an administrator to select from a multi-select list - exactly which groups are to be kept alive.	
			</p>
		</div>
		<div style="clear:both;"></div>
	</div>
	<hr class="bottom-dash"></hr> 
</div>
