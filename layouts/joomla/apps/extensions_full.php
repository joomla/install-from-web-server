<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$extension_data = $displayData['extensions'][0];
$breadcrumbs = $displayData['breadcrumbs'];
$tags = explode('|', trim($extension_data->fields->get('36')));
if(JDEBUG) {
	var_dump($extension_data);
}
?>
<div class="item-view">
	<div class="grid-header">
		<ul class="breadcrumb">
			<li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl('view=dashboard'); ?>"><?php echo JText::_('COM_APPS_EXTENSIONS'); ?></a></li>
			<?php foreach ($breadcrumbs as $bc) : ?>
			<span class="divider"> / </span>
			<li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$bc->id}"); ?>"><?php echo $bc->name; ?></a></li>
			<?php endforeach; ?>
			<span class="divider"> / </span>
			<li><?php echo $extension_data->link_name; ?></li>
		</ul>
	</div>
	<div class="full-item-container">
		<h2>
			<span><?php echo $extension_data->link_name; ?></span> 
			<?php if (in_array('mod', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="badge jbadge badge-jmodule">M</span> 
			<?php endif; ?>
			<?php if (in_array('plugin', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="badge jbadge badge-jplugin">P</span> 
			<?php endif; ?>
			<?php if (in_array('esp', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="badge jbadge badge-jspecial">S</span> 
			<?php endif; ?>
			<?php if (in_array('tool', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="badge jbadge badge-jtool">T</span> 
			<?php endif; ?>
			<?php if (in_array('com', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="badge jbadge badge-jcomponent">C</span> 
			<?php endif; ?>
			<?php if (in_array('lang', $tags)) : ?>
			<span title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="badge jbadge badge-jlanguage">L</span>
			<?php endif; ?>
		</h2>
		
		<div id="item-left-container" class="pull-left">
			<img class="img img-polaroid" src="<?php echo $extension_data->image; ?>" />
		</div>
		
		<div class="item-info-container pull-left">

			<span class="item-version">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VERSION', $extension_data->fields->get('43'), JHTML::date($extension_data->link_modified)); ?>
			</span>
			<br />

			<?php if ($extension_data->fields->get('49')): ?>
			<span class="item-license">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_LICENSE', $extension_data->fields->get('49')); ?>
			</span>
			<?php endif; ?>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			<span><?php echo $extension_data->fields->get('50'); ?></span>
			<br />
			
			<span class="item-addedon">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_ADDEDON', JHTML::date($extension_data->link_created)); ?>
			</span>
			<br />

			<div class="rating">
				<?php for ($i = 1; $i < 6; $i++) : ?>
					<?php if ($extension_data->link_rating + 0.5 >= $i) : ?>
				<span class="icon-star"></span>
					<?php else : ?>
				<span class="icon-star-empty"></span>
					<?php endif; ?>
				<?php endfor; ?>
				<a target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data) . '#action'; ?>">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS', $extension_data->link_votes, $extension_data->reviews); ?>
				</a>
			</div>

		</div>
		
		<div class="clearfix"></div>
		
		<?php if ($extension_data->type > 1): ?>
		<form action="<?php echo $extension_data->downloadurl; ?>" method="post" onsubmit="return Joomla.installfromwebexternal('<?php echo $extension_data->downloadurl; ?>');">
			<input id="joomlaapsinstallatinput" type="hidden" name="installat" value="" /> 
			<input type="hidden" name="installapp" value="<?php echo $extension_data->link_id; ?>" /> 
		<?php endif; ?>
		<div class="item-buttons form-actions">
			<a target="_blank" class="btn btn-secondary" href="<?php echo AppsHelper::getJEDUrl($extension_data); ?>"><?php echo JText::_('COM_APPS_DIRECTORY_LISTING'); ?></a> 
			<a target="_blank" class="btn btn-secondary" href="<?php echo $extension_data->website; ?>"><?php echo JText::_('COM_APPS_DEVELOPER_WEBSITE'); ?></a> 
			<?php if ((!$extension_data->type || $extension_data->type == 0) && $extension_data->fields->get('29')): ?>
			<a target="_blank" class="install btn btn-success" href="<?php echo $extension_data->fields->get('29'); ?>"><?php echo JText::_('COM_APPS_INSTALL_DOWNLOAD_EXTERNAL'); ?></a>
			<?php elseif ($extension_data->type == 1): ?>
			<a class="install btn btn-success" href="#" onclick="Joomla.installfromweb('<?php echo $extension_data->downloadurl; ?>', '<?php echo $extension_data->link_name; ?>')"><?php echo JText::_('COM_APPS_INSTALL'); ?></a>
			<?php elseif ($extension_data->type == 2): ?>
			<button class="install btn btn-success" type="submit"><?php echo JText::_('COM_APPS_INSTALL_REGISTER'); ?></button>
			<?php elseif ($extension_data->type == 3): ?>
			<button class="install btn btn-success" type="submit"><?php echo JText::_('COM_APPS_INSTALL_PURCHASE'); ?></button>
			<?php endif; ?>

		</div>
		<?php if ($extension_data->type > 1): ?>
		</form>
		<?php endif; ?>
		<div class="item-desc">
			<p class="item-desc-title">
				<strong><?php echo $extension_data->link_name; ?></strong> 
				<?php if ($extension_data->fields->get('39')): ?>
					<?php echo JText::sprintf('COM_APPS_EXTENSION_AUTHOR', $extension_data->fields->get('39')); ?>
				<?php endif; ?>
			</p>
			<p class="item-desc-text" align="justify">
				<?php echo nl2br($extension_data->link_desc); ?>
			</p>
		</div>
		<div style="clear:both;"></div>
	</div>
	<hr class="bottom-dash"></hr> 
</div>
