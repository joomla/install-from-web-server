<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$app = JFactory::getApplication();
$filter_search = str_replace('_', ' ', $app->input->getString('filter_search'));
$view = $app->input->getCmd('view');
$list = $app->input->getCmd('list', 'grid');
$orderby = $app->input->get('ordering', 'score');
if (array_key_exists('orderby', $displayData))
{
	$orderby = $displayData['orderby'];
}

// Sorting Options
$ordering_options[] = JHtml::_('select.option', 'num_reviews', JText::_('COM_APPS_SORT_BY_REVIEWS'));
$ordering_options[] = JHtml::_('select.option', 'score', JText::_('COM_APPS_SORT_BY_SCORE'));
$ordering_options[] = JHtml::_('select.option', 'core_title', JText::_('COM_APPS_SORT_BY_NAME'));
$ordering_options[] = JHtml::_('select.option', 'core_created_time', JText::_('COM_APPS_SORT_BY_CREATED'));
$ordering_options[] = JHtml::_('select.option', 'core_modified_time', JText::_('COM_APPS_SORT_BY_UPDATED'));

$selected_ordering = $app->input->get('ordering', $orderby);
?>
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="com-apps-searchbox" placeholder="Search" value="<?php echo $filter_search; ?>" class="hasTooltip" title="">
	</div>
	<div class="btn-group pull-left search">
		<button type="button" class="btn hasTooltip" title="" onclick="Joomla.apps.initiateSearch();" data-original-title="Search">
			<i class="icon-search"></i></button>
		<button type="button" class="btn hasTooltip" title="" data-original-title="Clear" id="search-reset">
			<i class="icon-remove"></i></button>
	</div>
	<div class="btn-group pull-right select">
		<?php 
			if ($view != 'extension')
				echo JHTML::_('select.genericlist', $ordering_options, 'ordering', null, 'value', 'text', $selected_ordering, 'com-apps-ordering'); 
		?>
	</div>
	
	<?php if ($view != 'extension') : ?>
	<div class="btn-group pull-right">
		<button type="button" class="btn grid-view<?php echo ($list == 'grid') ? ' active' : ''; ?>" id="btn-grid-view"><i class="icon-grid-view"></i></button>
		<button type="button" class="btn list-view<?php echo ($list == 'list') ? ' active' : ''; ?>" id="btn-list-view"><i class="icon-list-view"></i></button>
	</div>
	<?php endif; ?>
</div>
