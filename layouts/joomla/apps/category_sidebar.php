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
<div class="well sidebar-nav">
	<ul class="nav nav-list">
		<?php foreach ($displayData as $category) : ?>
			<li><a class="transcode" href="index.php?option=com_apps&view=extension&id=<?php echo $category->id; ?>"><?php echo $category->name; ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
