<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

//@TODO: MOve the single extension grid into a reusable JLayout

defined('JPATH_BASE') or die;
$i = 0;
$extensions_perrow = $displayData['params']->get('extensions_perrow');
$spanclass = 'span' . (12 / $extensions_perrow);
?>

<div class="row-fluid">
	<?php 
		foreach ($displayData['extensions'] as $extension) :
			$ratingwidth = round(70 * ($extension->rating / 5));
			if ($i != 0 && $i%$extensions_perrow == 0) {
	?>
</div>
<hr />
<div class="row-fluid">
	<?php } ?>
	<div class="<?php echo $spanclass; ?>">
		<h4 class="center muted">
			<a class="transcode" href="index.php?option=com_apps&view=extension&id=<?php echo $extension->id; ?>"><?php echo $extension->name; ?></a>
		</h4>
		<p class="center">
			<a class="transcode" href="index.php?option=com_apps&view=extension&id=<?php echo $extension->id; ?>">
			<img src="<?php echo $extension->image; ?>" class="img-polaroid" />
			</a>
		</p>
		<p class="rating center"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></p>
		<p class="center"><a class="btn btn-primary" href="#" onclick="Joomla.installfromweb('<?php echo $extension->downloadurl; ?>', '<?php echo $extension->name; ?>')">Install »</a> <a class="btn" href="#">Details »</a></p>
	</div>
	<?php $i++; endforeach; ?>
</div>
