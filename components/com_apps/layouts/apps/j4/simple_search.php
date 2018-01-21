<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$app     = Factory::getApplication();
$search  = str_replace('_', ' ', $app->input->getString('filter_search'));
$view    = $app->input->getCmd('view');
$list    = $app->input->getCmd('list', 'grid');
$orderby = $displayData['orderby'] ?? $app->input->get('ordering', '');

// Sorting Options
$orderingOptions = [
	HTMLHelper::_('select.option', '', Text::_('COM_APPS_SORT_BY_JED_DEFAULT')),
	HTMLHelper::_('select.option', 'num_reviews', Text::_('COM_APPS_SORT_BY_REVIEWS')),
	HTMLHelper::_('select.option', 'score', Text::_('COM_APPS_SORT_BY_SCORE')),
	HTMLHelper::_('select.option', 'core_title', Text::_('COM_APPS_SORT_BY_NAME')),
	HTMLHelper::_('select.option', 'core_created_time', Text::_('COM_APPS_SORT_BY_CREATED')),
	HTMLHelper::_('select.option', 'core_modified_time', Text::_('COM_APPS_SORT_BY_UPDATED')),
];

$selectedOrdering = $app->input->get('ordering', $orderby);
?>
<div class="row">
	<div id="filter-bar" class="col mb-3">
		<div class="form-row">
			<div class="col-md">
				<div class="input-group">
					<input type="text" name="filter_search" id="com-apps-searchbox" placeholder="<?php echo Text::_('COM_APPS_SEARCH_BTN'); ?>" value="<?php echo $search; ?>" class="hasTooltip form-control">
					<div class="input-group-append">
						<button type="button" class="btn btn-outline-secondary hasTooltip" onclick="Joomla.apps.initiateSearch();" data-original-title="<?php echo Text::_('COM_APPS_SEARCH_BTN'); ?>"><span class="fa fa-search" aria-hidden="true"></span></button>
						<button type="button" class="btn btn-outline-secondary hasTooltip" data-original-title="<?php echo Text::_('COM_APPS_CANCEL_BTN'); ?>" id="search-reset"><span class="fa fa-times" aria-hidden="true"></span></button>
					</div>
				</div>
			</div>
			<?php if ($view != 'extension') : ?>
				<div class="col-md">
					<?php echo HTMLHelper::_('select.genericlist', $orderingOptions, 'ordering', 'class="custom-select"', 'value', 'text', $selectedOrdering, 'com-apps-ordering'); ?>
				</div>
				<div class="col-md">
					<div class="btn-group float-md-right">
						<button type="button" class="btn btn-secondary grid-view<?php echo $list === 'grid' ? ' active' : ''; ?>" id="btn-grid-view"><span class="fa fa-th" aria-hidden="true"></span></button>
						<button type="button" class="btn btn-secondary list-view<?php echo $list === 'list' ? ' active' : ''; ?>" id="btn-list-view"><span class="fa fa-list" aria-hidden="true"></span></button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
