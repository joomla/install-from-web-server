<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$extension_data = $displayData['extension']; //print_r($extension_data);
$tags = explode('|', trim($extension_data->fields->get('36')));
?>
<li class="item <?php echo $displayData['spanclass']; ?>">
<div class="thumbnail">
	<p class="rating center">
		<?php for ($i = 1; $i < 6; $i++) : ?>
			<?php if ($extension_data->rating + 0.5 >= $i) : ?>
		<span class="icon-star"></span>
			<?php else : ?>
		<span class="icon-star-empty"></span>
			<?php endif; ?>
		<?php endfor; ?>
	</p>
	<div class="center item-image">
		<a class="transcode ajaxloaded" href="<?php echo AppsHelper::getAJAXUrl("view=extension&id={$extension_data->id}"); ?>">
			<img src="<?php echo $extension_data->image; ?>" class="img center" />
		</a>
	</div>
	<ul class="item-type center">
		<?php if (in_array('mod', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="badge badge-jmodule">M</span> 
		<?php endif; ?>
		<?php if (in_array('plugin', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="badge badge-jplugin">P</span> 
		<?php endif; ?>
		<?php if (in_array('esp', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="badge badge-jspecial">S</span> 
		<?php endif; ?>
		<?php if (in_array('tool', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="badge badge-jtool">T</span> 
		<?php endif; ?>
		<?php if (in_array('com', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="badge badge-jcomponent">C</span> 
		<?php endif; ?>
		<?php if (in_array('lang', $tags)) : ?>
		<span title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="badge badge-jlanguage">L</span>
		<?php endif; ?>
	</ul>
	<h4 class="center muted">
		<a class="transcode ajaxloaded" href="<?php echo AppsHelper::getAJAXUrl("view=extension&id={$extension_data->id}"); ?>"><?php echo $extension_data->name; ?></a>
	</h4>
	<p class="item-description">
		<?php echo substr($extension_data->description, 0, 200); ?>
	</p>
</div>
</li>
