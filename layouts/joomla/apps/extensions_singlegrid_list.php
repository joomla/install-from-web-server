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
$commercial = strtolower($extension_data->fields->get('50')) != "free" ? true : false;
?>
<li class="item <?php echo $displayData['spanclass']; ?>">
<div class="thumbnail">
	<div class="item-header" onclick="Joomla.loadweb(apps_base_url+'<?php echo AppsHelper::getAJAXUrl("view=extension&id={$extension_data->id}"); ?>');">
		<h4 class="muted">
			<a class="transcode ajaxloaded" href="<?php echo AppsHelper::getAJAXUrl("view=extension&id={$extension_data->id}"); ?>"><?php echo trim($extension_data->name); ?></a>
		</h4>
	</div>
	<div class="item-description" onclick="Joomla.loadweb(apps_base_url+'<?php echo AppsHelper::getAJAXUrl("view=extension&id={$extension_data->id}"); ?>');">
		<?php echo mb_strlen(trim($extension_data->description)) > 400 ? mb_substr(trim($extension_data->description), 0, mb_stripos(trim($extension_data->description), ' ', 400)) . '...' : trim($extension_data->description); ?>
		<div class="fader">&nbsp;</div>
	</div>
	<div class="item-icons">
		<div class="item-parts">
			<ul class="item-type center">
				<?php if ($commercial) : ?>
				<span title="<?php echo $extension_data->fields->get('50'); ?>" class="label label-jcommercial">$</span> 
				<?php endif; ?>
				<?php if (in_array('com', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="label label-jcomponent">C</span> 
				<?php endif; ?>
				<?php if (in_array('lang', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="label label-jlanguage">L</span>
				<?php endif; ?>
				<?php if (in_array('mod', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="label label-jmodule">M</span> 
				<?php endif; ?>
				<?php if (in_array('plugin', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="label label-jplugin">P</span> 
				<?php endif; ?>
				<?php if (in_array('esp', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="label label-jspecial">S</span> 
				<?php endif; ?>
				<?php if (in_array('tool', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="label label-jtool">T</span> 
				<?php endif; ?>
			</ul>
		</div>
		<div class="item-stars">
			<p class="rating center">
				<?php for ($i = 1; $i < 6; $i++) : ?>
					<?php if ($extension_data->rating + 0.25 >= $i) : ?>
				<span class="icon-star"></span>
					<?php elseif ($i - $extension_data->rating >= 0.25 && $i - $extension_data->rating < 0.75) : ?>
				<span class="icon-star-2"></span>
					<?php else : ?>
				<span class="icon-star-empty"></span>
					<?php endif; ?>
				<?php endfor; ?>
				<br />
				<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data) . '#action'; ?>">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS_LIST', $extension_data->link_votes, $extension_data->reviews); ?>
				</a>
			</p>
		</div>
	</div>
</div>
</li>
