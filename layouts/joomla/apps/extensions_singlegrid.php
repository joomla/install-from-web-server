<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
<div class="item <?php echo $displayData['spanclass']; ?>">
	<p class="rating center"><i class="icon-star rated"></i><i class="icon-star rated"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></p>
	<div class="item-image">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $displayData['extension']->id; ?>&format=raw">
			<img src="<?php echo $displayData['extension']->image; ?>" class="" />
		</a>
	</div>
	<!-- <p class="center"><a class="btn btn-primary" href="#" onclick="Joomla.installfromweb('<?php echo $displayData['extension']->downloadurl; ?>', '<?php echo $displayData['extension']->name; ?>')">Install »</a> <a class="btn" href="#">Details »</a></p> -->
	<ul class="item-type">
		<li class="m">M</li>
		<li class="p">P</li>
		<li class="s">S</li>
		<li class="t">T</li>
		<li class="c">C</li>
	</ul>
	<h4 class="center muted">
		<a class="transcode ajaxloaded" href="index.php?option=com_apps&view=extension&id=<?php echo $displayData['extension']->id; ?>&format=raw"><?php echo $displayData['extension']->name; ?></a>
	</h4>
	<p class="item-description">
		Speed-up Joomla  administration with AJAX support! lorem ispum dolor sit amet
	</p>
</div>