<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('JPATH_BASE') or die;

/** @var Joomla\CMS\Layout\FileLayout $this */

?>
<?php foreach ($displayData as $category) : ?>
	<ul class="nav nav-list">
		<li<?php echo ($category->selected) ? ' class="active"' : ''; ?>>
			<a class="transcode<?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl(['view' => 'category', 'id' => $category->id]); ?>"><?php echo $category->name; ?></a>

			<?php if (count($category->children) && $category->active) : ?>
				<?php echo $this->render($category->children); ?>
			<?php endif; ?>
		</li>
	</ul>
<?php endforeach; ?>
