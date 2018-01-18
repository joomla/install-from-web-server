<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$extension_data = $displayData['extension'];
$tags = $extension_data->includes->value;
$commercial = $extension_data->type->value != "free" ? true : false;
?>
<div class="item <?php echo $displayData['spanclass']; ?>">
	<div class="card">
		<div class="rating card-header">
			<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data) . '#reviews'; ?>"><?php echo Text::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS_LIST', $extension_data->score->value, $extension_data->num_reviews->value); ?></a>
		</div>
		<img src="<?php echo $extension_data->image; ?>" class="card-img-top" />
		<div class="card-body" onclick="Joomla.loadweb(apps_base_url+'<?php echo AppsHelper::getAJAXUrl(['view' => 'extension', 'id' => $extension_data->id->value]); ?>');">
			<h4 class="card-title"><?php echo trim($extension_data->core_title->value); ?></h4>
			<ul class="item-type center">
				<?php if ($commercial) : ?>
					<span title="<?php echo $extension_data->type->value; ?>" class="label label-jcommercial">$</span>
				<?php endif; ?>
				<?php if (in_array('com', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_COMPONENT'); ?>" class="label label-jcomponent">C</span>
				<?php endif; ?>
				<?php if (in_array('lang', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_LANGUAGE'); ?>" class="label label-jlanguage">L</span>
				<?php endif; ?>
				<?php if (in_array('mod', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_MODULE'); ?>" class="label label-jmodule">M</span>
				<?php endif; ?>
				<?php if (in_array('plugin', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_PLUGIN'); ?>" class="label label-jplugin">P</span>
				<?php endif; ?>
				<?php if (in_array('esp', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="label label-jspecial">S</span>
				<?php endif; ?>
				<?php if (in_array('tool', $tags)) : ?>
					<span title="<?php echo Text::_('COM_APPS_TOOL'); ?>" class="label label-jtool">T</span>
				<?php endif; ?>
			</ul>
			<div class="card-text">
				<?php echo HTMLHelper::_('string.truncate', $extension_data->core_body->value, 400, false, true); ?>
			</div>
		</div>
	</div>
</div>
