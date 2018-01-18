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
<div class="com-apps-sidebar">
	<div class="card">
		<div class="card-body">
			<div class="card-title"><h3><?php echo JText::_('COM_APPS_CATEGORIES'); ?></h3></div>
			<ul class="nav flex-column">
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
					<li class="nav-item">
						<a class="nav-link transcode<?php echo $category->selected ? ' active' : ''; ?>" href="<?php echo AppsHelper::getAJAXUrl($ajaxUrlSegments); ?>"><?php echo $category->name; ?></a>

						<?php if ($category->active && count($category->children)) : ?>
							<?php echo $this->sublayout('children', $category->children); ?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
