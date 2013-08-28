<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$app = JFactory::getApplication();
$filter_search = $app->input->getWord('filter_search');
?>
<div class="com-apps-search">
	<input id="com-apps-searchbox" type="text" placeholder="<?php echo JText::_('COM_APPS_SEARCH'); ?>" value="<?php echo $filter_search; ?>" />
	<i class="icon-search"></i>
</div>
