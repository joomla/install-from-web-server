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

$app           = Factory::getApplication();
$search        = str_replace('_', ' ', $app->input->getString('filter_search'));
$view          = $app->input->getCmd('view');
$list          = $app->input->getCmd('list', 'grid');
$orderby       = $displayData['orderby'] ?? $app->input->get('ordering', '');
$version       = $displayData['version'] ?? $app->input->get('filter_version', 'current');
$pluginVersion = $displayData['pluginVersion'] ?? '0.0.0';

$supportsJoomlaVersionFilter = version_compare($pluginVersion, '2.1', '>=');

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
<div class="row-fluid btn-toolbar">
	<div class="span6">
		<div class="filter-search btn-group">
			<div class="input-append">
				<input type="text" name="filter_search" id="com-apps-searchbox" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $search; ?>" class="hasTooltip">
				<button type="button" class="btn hasTooltip" onclick="Joomla.apps.initiateSearch();" data-original-title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>" aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search" aria-hidden="true"></span></button>
				<button type="button" class="btn hasTooltip" data-original-title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" aria-label="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" id="search-reset"><span class="icon-remove" aria-hidden="true"></span></button>
			</div>
		</div>
	</div>
	<?php if ($view != 'extension') : ?>
		<div class="span6 text-right">
			<div class="btn-group">
				<button type="button" class="btn grid-view<?php echo ($list == 'grid') ? ' active' : ''; ?>" id="btn-grid-view" aria-label="<?php echo Text::_('COM_APPS_GRID_VIEW'); ?>"><span class="icon-grid-view" aria-hidden="true"></span></button>
				<button type="button" class="btn list-view<?php echo ($list == 'list') ? ' active' : ''; ?>" id="btn-list-view" aria-label="<?php echo Text::_('COM_APPS_LIST_VIEW'); ?>"><span class="icon-list-view" aria-hidden="true"></span></button>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php if ($view != 'extension') : ?>
	<div class="row-fluid btn-toolbar">
		<?php if ($supportsJoomlaVersionFilter) : ?>
			<div class="span6">
				<div class="btn-group select">
					<?php echo HTMLHelper::_('select.genericlist', $versionFilteringOptions, 'filter_version', null, 'value', 'text', $version, 'com-apps-filter-joomla-version'); ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="<?php echo $supportsJoomlaVersionFilter ? 'span6' : 'span12'; ?> text-right">
			<div class="btn-group select">
				<?php echo HTMLHelper::_('select.genericlist', $orderingOptions, 'ordering', null, 'value', 'text', $orderby, 'com-apps-ordering'); ?>
			</div>
		</div>
	</div>
<?php endif; ?>
