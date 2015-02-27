<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app                   = JFactory::getApplication();
$catid                 = $app->input->get('id', null, 'int');
$category_sidebar      = new JLayoutFile('joomla.apps.category_sidebar');
$extensions_imagegrid  = new JLayoutFile('joomla.apps.extensions_imagegrid');
$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid');
$advanced_search       = new JLayoutFile('joomla.apps.advanced_search');
$simple_search         = new JLayoutFile('joomla.apps.simple_search');
$current               = ($this->pagination->limitstart / $this->pagination->limit) + count($this->extensions);

$extension_data = array(
	'extensions' => $this->extensions,
	'breadcrumbs' => $this->breadcrumbs,
	'params' => $this->params,
	'total' => $this->total
);

$order_data = array(
	'orderby' => $this->orderby
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
					<?php echo $simple_search->render($order_data); ?>
				</div>
			</div>
			<?php echo $extensions_imagegrid->render($extension_data); ?>
		</div>
	</div>
</div>
