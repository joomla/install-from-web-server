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
		</h2>
		
		<div id="item-left-container" class="pull-left">
			<img class="img img-polaroid" src="<?php echo $extension_data->image; ?>" />
		</div>
		
		<div class="item-info-container pull-left">
			<div class="rating">
				<?php for ($i = 1; $i < 6; $i++) : ?>
					<?php if ($extension_data->link_rating + 0.25 >= $i) : ?>
				<span class="icon-star"></span>
					<?php elseif ($i - $extension_data->link_rating >= 0.25 && $i - $extension_data->link_rating < 0.75) : ?>
				<span class="icon-star-2"></span>
					<?php else : ?>
				<span class="icon-star-empty"></span>
					<?php endif; ?>
				<?php endfor; ?>
				&nbsp;
				<a class="transcode" target="_blank" href="<?php echo AppsHelper::getJEDUrl($extension_data) . '#action'; ?>">
				<?php echo JText::sprintf('COM_APPS_EXTENSION_VOTES_REVIEWS', $extension_data->link_votes, $extension_data->reviews); ?>
				</a>
			</div>
			<div class="item-version control-group">
				<div class="control-label">
					<?php echo JText::_('COM_APPS_EXTENSION_VERSION'); ?>
				</div>
				<div class="controls">
					<?php echo $extension_data->fields->get('43') . ' ' . JText::sprintf('COM_APPS_EXTENSION_LAST_UPDATE', JHTML::date($extension_data->link_modified)); ?>
				</div>
			</div>
			<?php if ($extension_data->fields->get('49')): ?>
			<div class="item-license control-group">
				<div class="control-label">
					<?php echo JText::_('COM_APPS_EXTENSION_LICENSE', $extension_data->fields->get('49')); ?>
				</div>
				<div class="controls">
					<?php echo $extension_data->fields->get('49'); ?>
					&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
					<span><?php echo $extension_data->fields->get('50'); ?></span>
				</div>
			</div>
			<?php endif; ?>
			<div class="item-addedon control-group">
				<div class="control-label">
					<?php echo JText::_('COM_APPS_EXTENSION_ADDEDON'); ?>
				</div>
				<div class="controls">
					<?php echo JHTML::date($extension_data->link_created); ?>
				</div>
			</div>
			<div class="item-badge-container">
				<?php if (in_array('com', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="badge jbadge badge-jcomponent"><?php echo JText::_('COM_APPS_COMPONENT'); ?></span>&nbsp;
				<?php endif; ?>
				<?php if (in_array('lang', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_LANGUAGE'); ?>" class="badge jbadge badge-jlanguage"><?php echo JText::_('COM_APPS_LANGUAGE'); ?></span>&nbsp;
				<?php endif; ?>
				<?php if (in_array('mod', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="badge jbadge badge-jmodule"><?php echo JText::_('COM_APPS_MODULE'); ?></span>&nbsp;
				<?php endif; ?>
				<?php if (in_array('plugin', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="badge jbadge badge-jplugin"><?php echo JText::_('COM_APPS_PLUGIN'); ?></span>&nbsp;
				<?php endif; ?>
				<?php if (in_array('esp', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="badge jbadge badge-jspecial"><?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?></span>&nbsp;
				<?php endif; ?>
				<?php if (in_array('tool', $tags)) : ?>
				<span title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="badge jbadge badge-jtool"><?php echo JText::_('COM_APPS_TOOL'); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="clearfix"></div>
		
		<?php if ($extension_data->type > 1): ?>
		<input id="joomlaapsinstallatinput" type="hidden" name="installat" value="" />
		<input id="joomlaapsinstallfrominput" type="hidden" name="installfrom" value="<?php echo $extension_data->downloadurl; ?>" /> 
		<input type="hidden" name="installapp" value="<?php echo $extension_data->link_id; ?>" /> 
		<?php endif; ?>
		<div class="item-buttons form-actions">
			<?php if ($extension_data->downloadurl && is_numeric($extension_data->type)): ?>
				<?php if ($extension_data->type == 0): ?>
				<a target="_blank" class="transcode install btn btn-success" href="<?php echo $extension_data->downloadurl; ?>"><span class="icon-download"></span> <?php echo JText::_('COM_APPS_INSTALL_DOWNLOAD_EXTERNAL') . "&hellip;"; ?></a>
				<?php elseif ($extension_data->type == 1): ?>
				<a class="install btn btn-success" href="#" onclick="return Joomla.installfromweb('<?php echo $extension_data->downloadurl; ?>', '<?php echo $extension_data->link_name; ?>')"><span class="icon-checkmark"></span> <?php echo JText::_('COM_APPS_INSTALL') . "&hellip;"; ?></a>
				<?php elseif ($extension_data->type == 2): ?>
				<button class="install btn btn-success" id="appssubmitbutton" onclick="return Joomla.installfromwebexternal('<?php echo $extension_data->downloadurl; ?>');" type="submit"><span class="icon-pencil"></span> <?php echo JText::_('COM_APPS_INSTALL_REGISTER') . "&hellip;"; ?></button>
				<?php elseif ($extension_data->type == 3): ?>
				<button class="install btn btn-success" id="appssubmitbutton" onclick="return Joomla.installfromwebexternal('<?php echo $extension_data->downloadurl; ?>');" type="submit"><span class="icon-cart"></span> <?php echo JText::_('COM_APPS_INSTALL_PURCHASE') . "&hellip;"; ?></button>
				<?php endif; ?>&nbsp;&nbsp;&nbsp;
			<?php elseif ($extension_data->fields->get('29')) : ?>
				<?php if ((is_numeric($extension_data->type) && $extension_data->type == 0) || $extension_data->type == 1 || (strtolower($extension_data->fields->get('50')) == "non-commercial" && strtolower($extension_data->fields->get('38')) != "require registration to download")): ?>
				<a target="_blank" class="transcode install btn btn-success" href="<?php echo $extension_data->fields->get('29'); ?>"><span class="icon-download"></span> <?php echo JText::_('COM_APPS_INSTALL_DOWNLOAD_EXTERNAL') . "&hellip;"; ?></a>
				<?php elseif ($extension_data->type == 2 || (strtolower($extension_data->fields->get('50')) == "non-commercial" && strtolower($extension_data->fields->get('38')) == "require registration to download")): ?>
				<a class="install btn btn-success" href="<?php echo $extension_data->fields->get('29'); ?>"><span class="icon-pencil"></span> <?php echo JText::_('COM_APPS_INSTALL_REGISTER_DOWNLOAD_EXTERNAL') . "&hellip;"; ?></a>
				<?php elseif ($extension_data->type == 3 || (strtolower($extension_data->fields->get('50')) != "non-commercial")): ?>
				<a class="install btn btn-success" href="<?php echo $extension_data->fields->get('29'); ?>"><span class="icon-cart"></span> <?php echo JText::_('COM_APPS_INSTALL_PURCHASE_EXTERNAL') . "&hellip;"; ?></a>
				<?php endif; ?>&nbsp;&nbsp;&nbsp;
			<?php endif; ?>
			<a target="_blank" class="transcode btn btn-primary" href="<?php echo AppsHelper::getJEDUrl($extension_data); ?>"><span class="icon-list"></span> <?php echo JText::_('COM_APPS_DIRECTORY_LISTING'); ?></a>
			<?php if ($extension_data->website) : ?>
			&nbsp;&nbsp;&nbsp;
			<a target="_blank" class="transcode btn btn-primary" href="<?php echo $extension_data->website; ?>"><span class="icon-share-alt"></span> <?php echo JText::_('COM_APPS_DEVELOPER_WEBSITE'); ?></a>
			<?php endif; ?>
		</div>
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
		<div class="clearfix"></div>
	</div>
	<hr class="bottom-dash"></hr> 
</div>
