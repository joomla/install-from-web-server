<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');

$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar');
$extensions_imagegrid 	= new JLayoutFile('joomla.apps.extensions_imagegrid');
$extension_data			= array('extensions' => $this->extensions, 'params' => $this->params);
?>
<div class="row-fluid">
	<div class="span3">
		<?php echo $category_sidebar->render($this->categories); ?>
	</div>
	<div class="span9">
		<?php echo $extensions_imagegrid->render($extension_data); ?>
	</div>
</div>
