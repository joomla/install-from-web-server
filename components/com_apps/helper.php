<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Apps Helper
 *
 * @since  1.0.0
 */
class AppsHelper
{
	/**
 	 * Static Method to get the AJAX URL
 	 *
	 * @param   string   The fragment we whant to call
	 *
	 * @return  string   The AJAX URL for the JED
	 *
	 * @since   1.0.0
 	 */
	static function getAJAXUrl($fragment)
	{
		$componentParams = JComponentHelper::getParams('com_apps');
		$route_prefix    = $componentParams->get('route_prefix', 'index.php?option=com_apps&format=json');

		if (!$route_prefix)
		{
			return $fragment;
		}

		$uri   = JURI::getInstance($route_prefix);
		$query = $uri->getQuery();
		$query .= '&' . $fragment;
		$uri->setQuery($query);
		$url = $uri->toString();

		return $url;
	}

	/**
 	 * Static Method to get the JED URL for a item
 	 *
	 * @param   Item objekt that store the ID
	 *
	 * @return  string   The URL for the JED
	 *
	 * @since   1.0.0
 	 */
	static function getJEDUrl($item)
	{
		$url = 'http://extensions.joomla.org/';

		if (!isset($item->id->value))
		{
			return $url;
		}

		$url .= 'index.php?option=com_jed&view=extension&layout=default&id=' . $item->id->value;

		return $url;
	}

	/**
 	 * Static Method to get the URL for a JED category
 	 *
	 * @param   int      Category ID
	 *
	 * @return  string   The URL for a JED category
	 *
	 * @since   1.0.0
 	 */
	static function getJEDCatUrl($catid = 0)
	{
		$url = 'http://extensions.joomla.org/';

		if (!$catid)
		{
			return $url;
		}

		//$url .= 'index.php?option=com_jed&view=category&layout=list&id=' . $catid;
		$url .= 'index.php?option=com_jed&controller=filter&view=extension&layout=list&Itemid=145&filter[core_catid]=' . $catid;

		return $url;
	}
}
