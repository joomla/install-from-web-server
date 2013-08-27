<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
<div class="item <?php echo $displayData['spanclass']; ?>">
	<p class="rating center"><i class="icon-star rated"></i><i class="icon-star rated"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></p>
	<div class="item-image">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $displayData['extension']->id; ?>&format=json">
			<img src="<?php echo $displayData['extension']->image; ?>" class="" />
		</a>
	</div>
	<ul class="item-type">
		<li title="<?php echo JText::_('COM_APPS_MODULE'); ?>" class="m">M</li>
		<li title="<?php echo JText::_('COM_APPS_PLUGIN'); ?>" class="p">P</li>
		<li title="<?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?>" class="s">S</li>
		<li title="<?php echo JText::_('COM_APPS_TOOL'); ?>" class="t">T</li>
		<li title="<?php echo JText::_('COM_APPS_COMPONENT'); ?>" class="c">C</li>
	</ul>
	<h4 class="center muted">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $displayData['extension']->id; ?>&format=json"><?php echo $displayData['extension']->name; ?></a>
	</h4>
	<p class="item-description">
		Speed-up Joomla  administration with AJAX support! lorem ispum dolor sit amet
	</p>
</div>