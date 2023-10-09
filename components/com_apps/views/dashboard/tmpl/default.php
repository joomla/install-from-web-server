<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/** @var AppsViewExtension $this */

// Get the Joomla version
$joomlaVersion = Version::getShortVersion();

// Compare the version to determine the major version
if (version_compare($joomlaVersion, '5.0', 'ge')) {
    $majorVersion = 'j5'; // Handle Joomla 5.0 or newer
} elseif (version_compare($joomlaVersion, '4.0', 'ge')) {
    $majorVersion = 'j4'; // Handle Joomla 4.x
} else {
    $majorVersion = 'j3'; // Handle Joomla 3.x or older
}

if ($majorVersion === 'j3') {
    // Load the Joomla 3 template
    echo $this->loadTemplate('j3');
} elseif ($majorVersion === 'j4') {
    // Load the Joomla 4 template
    echo $this->loadTemplate('j4');
} elseif ($majorVersion === 'j5') {
    // Handle Joomla 5.0 or newer here
    echo $this->loadTemplate('j5');
} else {
    // Handle unexpected Joomla versions here
    // todo
}
