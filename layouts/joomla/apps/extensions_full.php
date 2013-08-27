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
$breadcrumbs = $displayData['breadcrumbs'];

?>
<div class="item-view">
	<div class="grid-header">
		<div class="breadcrumbs">
			<a class="transcode" href="index.php?format=json&option=com_apps&view=dashboard"><?php echo JText::_('COM_APPS_EXTENSIONS'); ?></a>&nbsp;/&nbsp;
			<?php foreach ($breadcrumbs as $bc) : ?>
			<a class="transcode" href="index.php?format=json&option=com_apps&view=category&id=<?php echo $bc->id; ?>"><?php echo $bc->name; ?></a>&nbsp;/&nbsp;
			<?php endforeach; ?>
			<?php echo $extension_data->name; ?>
		</div>
	</div>
	<div class="full-item-container">
		<img class="item-logo" src="<?php echo $extension_data->image; ?>" />
		<div class="item-info-container">
			<div class="item-title"><?php echo $extension_data->name; ?></div>
			<div>
				<ul class="item-type">
					<?php if (in_array('mod', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="m">M</li>
					<?php endif; ?>
					<?php if (in_array('plugin', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="p">P</li>
					<?php endif; ?>
					<?php if (in_array('esp', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="s">S</li>
					<?php endif; ?>
					<?php if (in_array('tool', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="t">T</li>
					<?php endif; ?>
					<?php if (in_array('com', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="c">C</li>
					<?php endif; ?>
					<?php if (in_array('lang', $extension_data->tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="l">L</li>
					<?php endif; ?>
				</ul>
			</div>
		
			<div class="rating">
				<?php for ($i = 1; $i < 6; $i++) : ?>
					<?php if ($extension_data->rating + 0.5 >= $i) : ?>
				<i class="icon-star rated"></i>
					<?php else : ?>
				<i class="icon-star"></i>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<div class="item-version">
				<?php echo $extension_data->version; ?>
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
				<?php echo $extension_data->name; ?>
			</p>
			<p class="item-desc-text">
				<?php echo $extension_data->description; ?>
			</p>
		</div>
		<div style="clear:both;"></div>
	</div>
	<hr class="bottom-dash"></hr> 
</div>
