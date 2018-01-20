<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/** @var AppsViewDashboard $this */

$majorVersion = version_compare($this->release, '4.0', 'ge') ? 'j4' : 'j3';

if ($majorVersion === 'j3')
{
	echo $this->loadTemplate('j3');
}
else
{
	echo $this->loadTemplate('j4');
}
