<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Apps View
 */
class AppsViewApps extends JViewLegacy
{
	/**
	 * Apps view display method
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Set the toolbar
		$this->addToolBar();

		// Get the configuration and other infos
		$this->getInfos();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	protected function addToolBar() 
	{
		JToolbarHelper::title(JText::_('COM_APPS_ADMINISTRATION'), '');
		JToolBarHelper::preferences('com_apps');
	}

	/**
	 * Get different configuration for the Apps component
	 * 
	 * @since  1.0.0
	 *
	 * @return void
	 */
	protected function getInfos()
	{
		// Get component params
		$this->params = JComponentHelper::getParams('com_apps');

		// Get the current Component Version
		$servermanifest      = JInstaller::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/manifest.xml');
		$this->serverVersion = $servermanifest['version'];

		// Get all client versions
		$clientXml      = simplexml_load_file('http://appscdn.joomla.org/webapps/jedapps/webinstaller.xml');
		$count          = $clientXml->count();
		$clientVersions = array();

		for ($i = 0; $i < $count; $i++)
		{
			$ifwc             = $clientXml->update[$i];
			$clientVersions[] = array(
				'version'               => (string)$ifwc->version,
				'targetplatformversion' => (string)$ifwc->targetplatform['version'],
			);
		}

		$this->clientVersions = $clientVersions;
	}
}
