<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Layout\FileLayout;

$category_sidebar = new FileLayout('joomla.apps.category_sidebar_ul');
?>
<div class="com-apps-sidebar">
	<div class="scroll-pane well">
		<ul class="nav nav-list">
			<li class="nav-header"><h3><?php echo JText::_('COM_APPS_CATEGORIES'); ?></h3></li>
			<?php foreach ($displayData as $category) : ?>
				<?php
				$ajaxUrlSegments = [];

				if ($category->id)
				{
					$ajaxUrlSegments['view'] = 'category';
					$ajaxUrlSegments['id']   = $category->id;
				}
				else
				{
					$ajaxUrlSegments['view'] = 'dashboard';
				}
				?>
				<li<?php echo ($category->selected) ? ' class="active"' : ''; ?>>
					<a class="transcode<?php echo $category->selected ? ' selected' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl($ajaxUrlSegments); ?>"><?php echo $category->name; ?></a>

					<?php if ($category->active && count($category->children)) : ?>
						<?php echo $category_sidebar->render($category->children); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
