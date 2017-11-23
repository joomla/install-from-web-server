<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

if (!version_compare(PHP_VERSION, '7.0', 'ge'))
{
	throw new RuntimeException('The Install from Web server component requires PHP 7.0 or greater');
}

$controller = BaseController::getInstance('Apps');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
