<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$extension_data = $displayData['extension'];
?>
<div class="item <?php echo $displayData['spanclass']; ?>">
	<p class="rating center">
		<?php for ($i = 1; $i < 6; $i++) : ?>
			<?php if ($extension_data->rating >= $i) : ?>
		<i class="icon-star rated"></i>
			<?php else : ?>
		<i class="icon-star"></i>
			<?php endif; ?>
		<?php endfor; ?>
	</p>
	<div class="item-image">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $extension_data->id; ?>&format=json">
			<img src="<?php echo $extension_data->image; ?>" class="" />
		</a>
	</div>
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
	<h4 class="center muted">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $extension_data->id; ?>&format=json"><?php echo $displayData['extension']->name; ?></a>
	</h4>
	<p class="item-description">
		<?php echo $extension_data->description; ?>
	</p>
</div>