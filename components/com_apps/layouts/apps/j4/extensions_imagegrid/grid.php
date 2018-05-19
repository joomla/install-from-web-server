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
$commercial = $extension_data->type->value !== 'free' ? true : false;
?>
<div class="item <?php echo $displayData['spanclass']; ?>">
	<div class="card">
		<div class="rating card-header text-center">
			<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data) . '#reviews'; ?>"><?php echo Text::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS_LIST', $extension_data->score->value, $extension_data->num_reviews->value); ?></a>
		</div>
		<img src="<?php echo $extension_data->image; ?>" class="card-img-top" />
		<div class="card-body load-extension" data-url="<?php echo AppsHelper::getAJAXUrl(['view' => 'extension', 'id' => $extension_data->id->value]); ?>">
			<h4 class="card-title text-center"><?php echo trim($extension_data->core_title->value); ?></h4>
			<div class="card-text">
				<div class="item-type text-center">
					<?php if ($commercial) : ?>
						<span title="<?php echo $extension_data->type->value; ?>" class="badge badge-warning">$</span>
					<?php endif; ?>
					<?php if (in_array('com', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_COMPONENT'); ?>" class="badge badge-success">C</span>
					<?php endif; ?>
					<?php if (in_array('lang', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_LANGUAGE'); ?>" class="badge badge-dark">L</span>
					<?php endif; ?>
					<?php if (in_array('mod', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_MODULE'); ?>" class="badge badge-danger">M</span>
					<?php endif; ?>
					<?php if (in_array('plugin', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_PLUGIN'); ?>" class="badge badge-secondary">P</span>
					<?php endif; ?>
					<?php if (in_array('esp', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="badge badge-primary">S</span>
					<?php endif; ?>
					<?php if (in_array('tool', $tags)) : ?>
						<span title="<?php echo Text::_('COM_APPS_TOOL'); ?>" class="badge badge-light">T</span>
					<?php endif; ?>
				</div>
				<p><?php echo HTMLHelper::_('string.truncate', $extension_data->core_body->value, 400, false, true); ?></p>
			</div>
		</div>
	</div>
</div>
