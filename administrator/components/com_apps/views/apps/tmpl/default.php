<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<form action="<?php echo JRoute::_('index.php?option=com_apps'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<div class="alert alert-block text-center">
		<strong><?php echo JText::_('COM_APPS_EXTENSION_VERSIONS'); ?></strong>
		<br><br>
		<?php echo JText::sprintf('COM_APPS_SERVER_VERSION', $this->serverVersion); ?>
		<br>
		<?php foreach ($this->clientVersions AS $clientVersion) : ?>
		<?php echo JText::sprintf('COM_APPS_CLIENT_VERSION', $clientVersion['version'], $clientVersion['targetplatformversion']); ?>
		<br>
		<?php endforeach; ?>
	</div>
</form>
