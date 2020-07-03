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
$version = $displayData['version'] ?? $app->input->get('filter_version', 'current');

// Sorting Options
$orderingOptions = [
	HTMLHelper::_('select.option', '', Text::_('COM_APPS_SORT_BY_JED_DEFAULT')),
	HTMLHelper::_('select.option', 'num_reviews', Text::_('COM_APPS_SORT_BY_REVIEWS')),
	HTMLHelper::_('select.option', 'score', Text::_('COM_APPS_SORT_BY_SCORE')),
	HTMLHelper::_('select.option', 'core_title', Text::_('COM_APPS_SORT_BY_NAME')),
	HTMLHelper::_('select.option', 'core_created_time', Text::_('COM_APPS_SORT_BY_CREATED')),
	HTMLHelper::_('select.option', 'core_modified_time', Text::_('COM_APPS_SORT_BY_UPDATED')),
];

// Version Filtering Options
$versionFilteringOptions = [
	HTMLHelper::_('select.option', 'all', Text::_('COM_APPS_FILTER_ALL_JOOMLA_VERSIONS')),
	HTMLHelper::_('select.option', 'current', Text::_('COM_APPS_FILTER_CURRENT_JOOMLA_VERSION')),
];

?>
<div class="form-row mb-3">
	<div class="col-md">
		<div class="input-group">
			<label for="filter_search" class="sr-only">
				<?php echo Text::_('JSEARCH_FILTER'); ?>
			</label>
			<input type="text" name="filter_search" id="com-apps-searchbox" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $search; ?>" class="form-control" inputmode="search">
			<div class="input-group-append">
				<button type="button" class="btn btn-primary" aria-label="<?php echo Text::_('JSEARCH_FILTER'); ?>" id="search-extensions">
					<span class="fa fa-search" aria-hidden="true"></span>
				</button>
				<button type="button" class="btn btn-secondary" aria-label="<?php echo Text::_('JCANCEL'); ?>" id="search-reset">
					<span class="fa fa-times" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
	<?php if ($view != 'extension') : ?>
		<div class="col-md">
			<div class="btn-group float-md-right">
				<button type="button" class="btn btn-secondary grid-view<?php echo $list === 'grid' ? ' active' : ''; ?>" id="btn-grid-view" aria-label="<?php echo Text::_('COM_APPS_GRID_VIEW'); ?>"><span class="fas fa-th" aria-hidden="true"></span></button>
				<button type="button" class="btn btn-secondary list-view<?php echo $list === 'list' ? ' active' : ''; ?>" id="btn-list-view" aria-label="<?php echo Text::_('COM_APPS_LIST_VIEW'); ?>"><span class="fas fa-list" aria-hidden="true"></span></button>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php if ($view != 'extension') : ?>
	<div class="form-row">
		<div class="col-md">
			<span class="sr-only">
				<label for="com-apps-filter-joomla-version"><?php echo Text::_('COM_APPS_FILTER_JOOMLA_VERSION'); ?></label>
			</span>
			<?php echo HTMLHelper::_('select.genericlist', $versionFilteringOptions, 'filter_version', 'class="custom-select"', 'value', 'text', $version, 'com-apps-filter-joomla-version'); ?>
		</div>
		<div class="col-md">
			<span class="sr-only">
				<label for="com-apps-ordering"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></label>
			</span>
			<?php echo HTMLHelper::_('select.genericlist', $orderingOptions, 'ordering', 'class="custom-select"', 'value', 'text', $orderby, 'com-apps-ordering'); ?>
		</div>
	</div>
<?php endif; ?>
