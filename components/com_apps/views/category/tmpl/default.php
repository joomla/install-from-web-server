<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app					= JFactory::getApplication();
$catid					= $app->input->get('id', null, 'int');
$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar');
$extensions_imagegrid 	= new JLayoutFile('joomla.apps.extensions_imagegrid');
$extensions_singlegrid 	= new JLayoutFile('joomla.apps.extensions_singlegrid');
$advanced_search		= new JLayoutFile('joomla.apps.advanced_search');
$simple_search			= new JLayoutFile('joomla.apps.simple_search');
$extension_data			= array('extensions' => $this->extensions, 'breadcrumbs' => $this->breadcrumbs, 'params' => $this->params, 'total' => $this->total);
$current 				= ($this->pagination->limitstart / $this->pagination->limit) + count($this->extensions);
?>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.japps.js"></script>
<div class="com-apps-container">
<div class="row-fluid">
	<div class="span3">
		<?php echo $category_sidebar->render($this->categories); ?>
	</div> 
	<div class="span9">
		<div class="row-fluid">
			<div class="span6">
				<?php echo $simple_search->render(array()); ?>
			</div>
			<div class="span6">
				<?php echo $advanced_search->render(array()); ?>
			</div>
		</div>

		<?php echo $extensions_imagegrid->render($extension_data); ?>
		<?php if ($this->total > $current): ?>
		<div align="center">
			<a class="transcode btn btn-primary" href="index.php?option=com_apps&format=json&view=category&id=<?php echo $catid; ?>&limitstart=<?php echo $this->pagination->next; ?>">Load More</a>
		</div>
		<?php endif; ?>
	</div>
</div>
</div>
