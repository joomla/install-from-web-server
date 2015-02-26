<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$category_sidebar = new JLayoutFile('joomla.apps.category_sidebar_ul');
?>
<div class="com-apps-sidebar">
	<div class="scroll-pane well">
		<ul class="nav nav-list">
			<li class="nav-header"><h3><?php echo JText::_('COM_APPS_CATEGORIES'); ?></h3></li>
			<?php foreach ($displayData as $category) : ?>
				<li<?php echo ($category->selected) ? ' class="active"' : ''; ?>>
					<a class="transcode<?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl(($category->id ? "view=category&id={$category->id}" : "view=dashboard")); ?>"><?php echo $category->name; ?></a>

					<?php if ($category->active && count($category->children)) : ?>
						<?php echo $category_sidebar->render($category->children); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
