<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$category_sidebar      = new JLayoutFile('joomla.apps.category_sidebar');
$extensions_imagegrid  = new JLayoutFile('joomla.apps.extensions_imagegrid');
$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid');
$extensions_full       = new JLayoutFile('joomla.apps.extensions_full');
$advanced_search       = new JLayoutFile('joomla.apps.advanced_search');
$simple_search         = new JLayoutFile('joomla.apps.simple_search');

$extension_data = array(
	'extensions'  => $this->extensions,
	'breadcrumbs' => $this->breadcrumbs,
	'params'      => $this->params
);
?>
<div class="com-apps-container">
	<div class="row-fluid">
		<div class="span3">
			<?php echo $category_sidebar->render($this->categories); ?>
		</div> 
		<div class="span9">
			<div class="row-fluid">
				<div class="span12">
					<?php echo $simple_search->render(array()); ?>
				</div>
			</div>
			<?php echo $extensions_full->render($extension_data); ?>
		</div>
	</div>
</div>
