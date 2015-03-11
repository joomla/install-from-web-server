<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_apps
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php';
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'component/com_apps/css/apps.css');

$controller = JControllerLegacy::getInstance('Apps');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
