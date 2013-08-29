<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar_ul');
?>
		<?php foreach ($displayData as $category) : ?>
	<ul class="<?php echo count($category->children) ? 'nav com-apps-list' : 'dummy-submenu'; ?>">
			<li><a class="transcode<?php echo $category->active ? ' active' : ''; ?><?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$category->id}"); ?>"><?php echo $category->name; ?></a>
			<?php if (count($category->children)) : ?>
				<?php echo $category_sidebar->render($category->children); ?>
			<?php endif; ?>
			</li>
	</ul>
		<?php endforeach; ?>
