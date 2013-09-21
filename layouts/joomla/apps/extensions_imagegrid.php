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

$view = $app->input->getCmd('view');
if ($view != 'dashboard') {
	$firstcrumb = '<a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(\'view=dashboard\'); ?>">' . JText::_('COM_APPS_EXTENSIONS') . '</a>';
}
else {
	$firstcrumb = JText::_('COM_APPS_EXTENSIONS_DASHBOARD');
}
$lastc = '';
$layouts = array('grid', 'list');
?>

<?php if (!count($displayData['extensions'])) : ?>
<div class="row-fluid">
	<div class="item-view span12">
		<div class='items grid-container'>
			<div class="grid-header">
				<ul class="breadcrumb">
					<li><?php echo $firstcrumb; ?></li>
					<?php foreach ($breadcrumbs as $bc) : ?>
					<span class="divider"> / </span>
					</li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$bc->id}"); ?>"><?php echo $bc->name; ?></a></li>
					<?php $lastc = $bc; endforeach; ?>
					
					<!-- Link to category on JED -->
					<li class="pull-right"><a href="<?php echo AppsHelper::getJEDCatUrl(is_object($lastc) ? $lastc->id : $lastc); ?>" target="_blank" title="<?php echo JText::_('COM_APPS_CATEGORY_JEDLINK'); ?>"><span class="icon-out-2"></span></a></li>
				</ul>
			</div>
			<div class="row-fluid">
				<blockquote><h4><?php echo JText::_('COM_APPS_NO_RESULTS_DESCRIPTION'); ?></h4></blockquote>
			</div>
		</div>
	</div>
</div>
<?php return; endif; ?>

<div class="row-fluid">
	<div class="item-view span12">
		<?php foreach ($layouts as $layout): ?>
		<div class="items <?php echo $layout; ?>-container<?php echo ($app->input->getCmd('list', 'grid') == $layout) ? '' : ' hidden'; ?>">

		<ul class="breadcrumb">
			<li><?php echo $firstcrumb; ?></li>
			<?php foreach ($breadcrumbs as $bc) : ?>
			<span class="divider"> / </span>
			</li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$bc->id}"); ?>"><?php echo $bc->name; ?></a></li>
			<?php $lastc = $bc; endforeach; ?>
			
			<!-- Link to category on JED -->
			<?php if (isset($lastc->id)) : ?>
			<li class="pull-right"><a href="<?php echo AppsHelper::getJEDCatUrl($lastc->id); ?>" target="_blank" title="<?php echo JText::_('COM_APPS_CATEGORY_JEDLINK'); ?>"><span class="icon-out-2"></span></a></li>
			<?php endif; ?>
		</ul>

		<ul class="thumbnails">
			<?php
				// Looping thru all the extensions, closing and starting a new row after every $extensions_perrow items
				// The single extension box is loaded using the JLayout
				$i = 0;
				foreach ($displayData['extensions'] as $extension) :
					$ratingwidth = round(70 * ($extension->rating / 5));
					if ($i != 0 && $i%$extensions_perrow == 0 && $layout != 'list') { 
			?>
			</ul>	
			<ul class="thumbnails">
			<?php 
					}

					$data	= array('spanclass' => $spanclass,'extension' => $extension);
					$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid_' . $layout);
					echo $extensions_singlegrid->render($data);
					
					$i++;
				endforeach;
			?>
		</ul>
		
		</div>
		<?php endforeach; ?>
	</div>
</div>
