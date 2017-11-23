<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;

/** @var AppsViewDashboard $this */

?>
<div class="com-apps-container">
	<div class="row-fluid">
		<div class="span3">
			<?php echo (new FileLayout('joomla.apps.category_sidebar'))->render($this->categories); ?>
		</div>
		<div class="span9">
			<div class="row-fluid">
				<div class="span12">
					<?php echo (new FileLayout('joomla.apps.simple_search'))->render(['orderby' => $this->orderby]); ?>
				</div>
			</div>

			<?php echo (new FileLayout('joomla.apps.extensions_imagegrid'))->render(['extensions' => $this->extensions, 'breadcrumbs' => $this->breadcrumbs]); ?>
		</div>
	</div>
</div>
