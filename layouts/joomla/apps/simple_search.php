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

// Sorting Options
$ordering_options[] = JHtml::_('select.option', 't2.link_hits', JText::_('COM_APPS_SORT_BY_POPULAR'));
$ordering_options[] = JHtml::_('select.option', 't2.link_name', JText::_('COM_APPS_SORT_BY_NAME'));
$ordering_options[] = JHtml::_('select.option', 't2.link_rating', JText::_('COM_APPS_SORT_BY_RATING'));
$ordering_options[] = JHtml::_('select.option', 't2.link_created', JText::_('COM_APPS_SORT_BY_CREATED'));

$selected_ordering = $app->input->get('ordering', 't2.link_hits');
?>
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="com-apps-searchbox" placeholder="Search" value="<?php echo $filter_search; ?>" class="hasTooltip" title="">
	</div>
	<div class="btn-group pull-left hidden-phone">
		<button type="button" class="btn hasTooltip" title="" onclick="Joomla.apps.initiateSearch();" data-original-title="Search">
			<i class="icon-search"></i></button>
		<button type="button" class="btn hasTooltip" title="" onclick="document.id('com-apps-searchbox').value='';" data-original-title="Clear">
			<i class="icon-remove"></i></button>
	</div>
	<div class="btn-group pull-right">
		<?php 
			if ($view != 'extension')
				echo JHTML::_('select.genericlist', $ordering_options, 'ordering', null, 'value', 'text', $selected_ordering, 'com-apps-ordering'); 
		?>
	</div>
	
	<?php if ($view != 'extension') : ?>
	<div class="btn-group pull-right">
		<button type="button" class="btn grid-view"><i class="icon-grid-view"></i></button>
		<button type="button" class="btn list-view"><i class="icon-list-view"></i></button>
	</div>
	<?php endif; ?>
</div>
