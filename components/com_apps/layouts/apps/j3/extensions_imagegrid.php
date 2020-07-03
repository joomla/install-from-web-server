<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

//@TODO: Move the single extension grid into a reusable JLayout
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\Layout\FileLayout $this */

$app = Factory::getApplication();

/** @var array $breadcrumbs */
$breadcrumbs = $displayData['breadcrumbs'];
$spanclass   = 'span3';

$view = $app->input->getCmd('view');

if ($view != 'dashboard')
{
	$firstcrumb = '<a class="transcode" href="' . AppsHelper::getAJAXUrl(['view' => 'dashboard']) . '">' . Text::_('COM_APPS_EXTENSIONS') . '</a>';
}
else
{
	$firstcrumb = Text::sprintf(
		'COM_APPS_POPULAR_EXTENSIONS_FROM_JED',
		HTMLHelper::_(
			'link',
			'https://extensions.joomla.org',
			Text::_('COM_APPS_JOOMLA_EXTENSIONS_DIRECTORY'),
			[
				'target' => '_blank',
				'rel'    => 'nofollow noopener',
			]
		)
	);
}
$lastc   = '';
$layouts = ['grid', 'list'];
?>

<?php if (!count($displayData['extensions'])) : ?>
	<div class="row-fluid">
		<div class="item-view span12">
			<div class="items grid-container">
				<div class="grid-header">
					<ul class="breadcrumb">
						<li><?php echo $firstcrumb; ?></li>
						<?php foreach ($breadcrumbs as $bc) : ?>
							<span class="divider"> / </span>
							<li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(['view' => 'category', 'id' => $bc->id]); ?>"><?php echo $bc->name; ?></a></li>
							<?php $lastc = $bc; ?>
						<?php endforeach; ?>

						<!-- Link to category on JED -->
						<li class="pull-right">
							<a href="<?php echo AppsHelper::getJEDCatUrl(is_object($lastc) ? $lastc->id : $lastc); ?>" target="_blank" title="<?php echo Text::_('COM_APPS_CATEGORY_JEDLINK'); ?>">
								<span class="icon-out-2"></span>
							</a>
						</li>
					</ul>
				</div>
				<div class="row-fluid">
					<blockquote><h4><?php echo Text::_('COM_APPS_NO_RESULTS_DESCRIPTION'); ?></h4></blockquote>
				</div>
			</div>
		</div>
	</div>
	<?php return; ?>
<?php endif; ?>

<div class="row-fluid">
	<div class="item-view span12">
		<?php foreach ($layouts as $layout) : ?>
			<div class="items <?php echo $layout; ?>-container<?php echo ($app->input->getCmd('list', 'grid') == $layout) ? '' : ' hidden'; ?>">
				<ul class="breadcrumb">
					<li><?php echo $firstcrumb; ?></li>
					<?php foreach ($breadcrumbs as $bc) : ?>
						<span class="divider"> / </span>
						<li><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(['view' => 'category', 'id' => $bc->id]); ?>"><?php echo $bc->name; ?></a></li>
						<?php $lastc = $bc; ?>
					<?php endforeach; ?>

					<!-- Link to category on JED -->
					<?php if (isset($lastc->id)) : ?>
						<li class="pull-right">
							<a href="<?php echo AppsHelper::getJEDCatUrl($lastc->id); ?>" target="_blank" title="<?php echo Text::_('COM_APPS_CATEGORY_JEDLINK'); ?>">
								<span class="icon-out-2"></span>
							</a>
						</li>
					<?php endif; ?>
				</ul>

				<ul class="thumbnails">
					<?php // Looping thru all the extensions, closing and starting a new row after every 4 items. The single extension box is loaded using the JLayout ?>
					<?php $i = 0; ?>
					<?php foreach ($displayData['extensions'] as $extension) : ?>
						<?php if ($i != 0 && $i % 4 == 0 && $layout != 'list') : ?>
							</ul>
							<ul class="thumbnails">
						<?php endif; ?>

						<?php echo $this->sublayout($layout, ['spanclass' => $spanclass, 'extension' => $extension]); ?>

						<?php $i++; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
</div>
