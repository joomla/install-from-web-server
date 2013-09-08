<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

//@TODO: MOve the single extension grid into a reusable JLayout
defined('JPATH_BASE') or die;
$componentParams = JComponentHelper::getParams('com_apps');
$app = JFactory::getApplication();
$data	= array();
$breadcrumbs = $displayData['breadcrumbs'];
$extensions_perrow = $componentParams->get('extensions_perrow', 4);
$spanclass = 'span' . (12 / $extensions_perrow);

$ordering_options[] = JHtml::_('select.option', 't2.link_name', JText::_('COM_APPS_SORT_BY_NAME'));
$ordering_options[] = JHtml::_('select.option', 't2.link_rating', JText::_('COM_APPS_SORT_BY_RATING'));
$ordering_options[] = JHtml::_('select.option', 't2.link_created', JText::_('COM_APPS_SORT_BY_CREATED'));

$selected_ordering = $app->input->get('ordering', 't2.link_rating');
$view = $app->input->getCmd('view');
if ($view != 'dashboard') {
	$firstcrumb = '<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(\'view=dashboard\'); ?>">' . JText::_('COM_APPS_EXTENSIONS') . '</a>';
}
else {
	$firstcrumb = JText::_('COM_APPS_EXTENSIONS_DASHBOARD');
}
?>

<?php if (!count($displayData['extensions'])) : ?>
<div class="row-fluid">
	<div class="item-view span12">
		<div class='grid-container'>
			<div class="grid-header">
				<div class="breadcrumbs">
					<?php echo JText::_('COM_APPS_NO_RESULTS'); ?>
				</div>
			</div>
			<div class="row-fluid">
				<h4><?php echo JText::_('COM_APPS_NO_RESULTS_DESCRIPTION'); ?></h4>
			</div>
		</div>
	</div>
</div>
<?php return; endif; ?>

<div class="row-fluid">
	<div class="item-view span12">
		<div class='grid-container'>
			<div class="grid-header">
			<div class="breadcrumbs">
				<?php echo $firstcrumb; ?>
				<?php foreach ($breadcrumbs as $bc) : ?>
				&nbsp;/&nbsp;<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$bc->id}"); ?>"><?php echo $bc->name; ?></a>
				<?php endforeach; ?>
			</div>
			<div class="sort-by pull-right">
				<?php 
					if ($view != 'dashboard')
						echo JHTML::_('select.genericlist', $ordering_options, 'ordering', null, 'value', 'text', $selected_ordering, 'com-apps-ordering'); 
				?>
			</div>
		</div>
		<div class="items grid-view-container">
			<div class="row-fluid">
			<?php
				// Looping thru all the extensions, closing and starting a new row after every $extensions_perrow items
				// The single extension box is loaded using the JLayout
				$i = 0;
				foreach ($displayData['extensions'] as $extension) :
					$ratingwidth = round(70 * ($extension->rating / 5));
					if ($i != 0 && $i%$extensions_perrow == 0) { 
			?>
				</div>
				<hr />
				<div class="row-fluid">
			<?php 
					}

					$data	= array('spanclass' => $spanclass,'extension' => $extension);
					$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid');
					echo $extensions_singlegrid->render($data);
					
					$i++;
				endforeach;
			?>
			</div>
		</div>
	</div>
</div>
