<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;

/** @var AppsViewExtension $this */

?>
<div class="com-apps-container">
	<div class="row-fluid">
		<div class="span3">
			<?php echo (new FileLayout('apps.j3.category_sidebar'))->render($this->categories); ?>
		</div>
		<div class="span9">
			<div class="row-fluid">
				<div class="span12">
					<?php echo (new FileLayout('apps.j3.simple_search'))->render(); ?>
				</div>
			</div>

			<?php echo (new FileLayout('apps.j3.extensions_full'))->render(['extension' => $this->extension, 'breadcrumbs' => $this->breadcrumbs]); ?>
		</div>
	</div>
</div>
