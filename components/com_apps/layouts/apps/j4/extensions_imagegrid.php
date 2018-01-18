<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

//@TODO: Move the single extension grid into a reusable JLayout
defined('JPATH_BASE') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

/** @var Joomla\CMS\Layout\FileLayout $this */

$app = Factory::getApplication();

/** @var array $breadcrumbs */
$breadcrumbs = $displayData['breadcrumbs'];
$spanclass   = 'col-md-6 col-lg-4 col-xl-3';

$view = $app->input->getCmd('view');

if ($view != 'dashboard')
{
	$firstcrumb = '<a class="transcode" href="' . AppsHelper::getAJAXUrl(['view' => 'dashboard']) . '">' . Text::_('COM_APPS_EXTENSIONS') . '</a>';
}
else
{
	$firstcrumb = Text::_('COM_APPS_EXTENSIONS_DASHBOARD');
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
					<blockquote><h4><?php echo Text::_('COM_APPS_NO_RESULTS_DESCRIPTION'); ?></h4></blockquote>
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
