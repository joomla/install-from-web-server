<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
//print_r($displayData);
$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar');
?>
<img  class="com-apps-logo" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/logo.png" alt=""/>
<div class="com-apps-sidebar sidebar-nav">
	<h3><?php echo JText::_('COM_APPS_CATEGORIES'); ?></h3>
	<div class="scroll-pane">
	<ul class="nav com-apps-list">
		<?php foreach ($displayData as $category) : ?>
			<li><a class="transcode" href="index.php?option=com_apps&format=raw&view=category&id=<?php echo $category->id; ?>"><?php echo $category->name; ?></a>
			<?php if (0 and count($category->children)) : ?>
				<ul class="dummy-submenu">
					<?php foreach ($category->children as $child): ?>
					<!--<li><a class="transcode" href="index.php?option=com_apps&view=category&id=<?php echo $child->id; ?>"><?php echo $child->name; ?><span>1234</span></a>-->
					<li><a class="transcode" href="index.php?option=com_apps&format=raw&view=category&id=<?php echo $child->id; ?>"><?php echo $child->name; ?></a>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ($category->active) : ?>
				<?php echo $category_sidebar->render($category->children); ?>
			<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	</div>
</div>
