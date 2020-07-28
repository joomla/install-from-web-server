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
$spanclass   = 'col-md-6 col-lg-4 col-xl-3';

$view = $app->input->getCmd('view');

if ($view != 'dashboard')
{
	$firstcrumb = HTMLHelper::_(
		'link',
		AppsHelper::getAJAXUrl(['view' => 'dashboard']),
		Text::_('COM_APPS_EXTENSIONS'),
		[
			'class' => 'transcode',
		]
	);
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
				'rel'    => 'noopener noreferrer',
			]
		)
	);
}
$lastc   = '';
$layouts = ['grid', 'list'];
?>

<?php if (!count($displayData['extensions'])) : ?>
	<div class="row">
		<div class="item-view col">
			<div class="items grid-container">
				<div class="grid-header">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><?php echo $firstcrumb; ?></li>
							<?php foreach ($breadcrumbs as $bc) : ?>
								<li class="breadcrumb-item"><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(['view' => 'category', 'id' => $bc->id]); ?>"><?php echo $bc->name; ?></a></li>
							<?php endforeach; ?>
						</ol>
					</nav>
				</div>
				<div class="row">
					<div class="col"><h4><?php echo Text::_('COM_APPS_NO_RESULTS_DESCRIPTION'); ?></h4></div>
				</div>
			</div>
		</div>
	</div>
	<?php return; ?>
<?php endif; ?>

<div class="row">
	<div class="item-view col">
		<?php foreach ($layouts as $layout) : ?>
			<div class="items <?php echo $layout; ?>-container<?php echo ($app->input->getCmd('list', 'grid') == $layout) ? '' : ' hidden'; ?>">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><?php echo $firstcrumb; ?></li>
						<?php foreach ($breadcrumbs as $bc) : ?>
							<li class="breadcrumb-item"><a class="transcode" href="<?php echo AppsHelper::getAJAXUrl(['view' => 'category', 'id' => $bc->id]); ?>"><?php echo $bc->name; ?></a></li>
						<?php endforeach; ?>
					</ol>
				</nav>

				<div class="<?php echo $layout === 'grid' ? 'row' : 'list-group'; ?>">
					<?php foreach ($displayData['extensions'] as $extension) : ?>
						<?php echo $this->sublayout($layout, ['spanclass' => $spanclass, 'extension' => $extension]); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
