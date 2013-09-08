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
$tags = explode('|', trim($extension_data->fields->get('36')));
?>
<div class="item-view">
	<div class="grid-header">
		<div class="breadcrumbs">
			<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl('view=dashboard'); ?>"><?php echo JText::_('COM_APPS_EXTENSIONS'); ?></a>
			<?php foreach ($breadcrumbs as $bc) : ?>
			&nbsp;/&nbsp;<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$bc->id}"); ?>"><?php echo $bc->name; ?></a>
			<?php endforeach; ?>
			&nbsp;/&nbsp;
			<?php echo $extension_data->link_name; ?>
		</div>
	</div>
	<div class="full-item-container">
		<img class="item-logo" src="<?php echo $extension_data->image; ?>" />
		<div class="item-info-container">
			<div class="item-title"><?php echo $extension_data->link_name; ?></div>
			<div>
				<ul class="item-type">
					
					<?php if (in_array('mod', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="m">M</li>
					<?php endif; ?>
					<?php if (in_array('plugin', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="p">P</li>
					<?php endif; ?>
					<?php if (in_array('esp', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="s">S</li>
					<?php endif; ?>
					<?php if (in_array('tool', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="t">T</li>
					<?php endif; ?>
					<?php if (in_array('com', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="c">C</li>
					<?php endif; ?>
					<?php if (in_array('lang', $tags)) : ?>
					<li title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="l">L</li>
					<?php endif; ?>
				</ul>
			</div>
		
			<div class="rating">
				<?php for ($i = 1; $i < 6; $i++) : ?>
					<?php if ($extension_data->link_rating + 0.5 >= $i) : ?>
				<i class="icon-star rated"></i>
					<?php else : ?>
				<i class="icon-star"></i>
					<?php endif; ?>
				<?php endfor; ?>
				<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data); ?>">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS', $extension_data->link_votes, $extension_data->reviews); ?>
				</a>
			</div>
			<div class="item-version">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VERSION', $extension_data->fields->get('43'), JHTML::date($extension_data->link_modified)); ?>
				
			</div>
		</div>
		<div style="clear:both;"></div>
		<?php if ($extension_data->type > 1): ?>
		<form action="<?php echo $extension_data->downloadurl; ?>" method="post" onsubmit="return Joomla.installfromwebexternal('<?php echo $extension_data->downloadurl; ?>');">
			<input id="joomlaapsinstallatinput" type="hidden" name="installat" value="" />
			<input type="hidden" name="installapp" value="<?php echo $extension_data->link_id; ?>" />
		<?php endif; ?>
		<div class="item-buttons">
			<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data); ?>"><?php echo JText::_('COM_APPS_DIRECTORY_LISTING'); ?></a>
			<a target="_blank" href="<?php echo $extension_data->website; ?>"><?php echo JText::_('COM_APPS_DEVELOPER_WEBSITE'); ?></a>
			<?php if (!$extension_data->type || $extension_data->type == 0): ?>
			
			<?php elseif ($extension_data->type == 1): ?>
			<a class="install" href="#" onclick="Joomla.installfromweb('<?php echo $extension_data->downloadurl; ?>', '<?php echo $extension_data->link_name; ?>')"><?php echo JText::_('COM_APPS_INSTALL'); ?></a>
			<?php elseif ($extension_data->type == 2): ?>
			<button class="install" type="submit"><?php echo JText::_('COM_APPS_INSTALL_REGISTER'); ?></button>
			<?php elseif ($extension_data->type == 3): ?>
			<button class="install" type="submit"><?php echo JText::_('COM_APPS_INSTALL_PURCHASE'); ?></button>
			<?php endif; ?>

		</div>
		<?php if ($extension_data->type > 1): ?>
		</form>
		<?php endif; ?>
		<div class="item-desc">
			<p class="item-desc-title">
				<?php echo $extension_data->link_name; ?> <small><?php echo JText::sprintf('COM_APPS_EXTENSION_AUTHOR', $extension_data->fields->get('39')); ?></small>
			</p>
			<p class="item-desc-text" align="justify">
				<?php echo nl2br($extension_data->link_desc); ?>
			</p>
		</div>
		<div style="clear:both;"></div>
	</div>
	<hr class="bottom-dash"></hr> 
</div>
