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
	<ul class="nav nav-list">
			<li<?php echo ($category->selected) ? ' class="active"' : ''; ?>><a class="transcode<?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl("view=category&id={$category->id}"); ?>"><?php echo $category->name; ?></a>
			<?php if (count($category->children) && $category->active) : ?>
				<?php echo $category_sidebar->render($category->children); ?>
			<?php endif; ?>
			</li>
	</ul>
		<?php endforeach; ?>
