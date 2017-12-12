<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

/**
 * Helper class for the Install From Web component.
 *
 * @since  1.0
 */
class AppsHelper
{
	/**
	 * Retrieve the Install From Web AJAX URL for a request.
	 *
	 * @param   array  $variables  The query variables to append to the base URI
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public static function getAJAXUrl(array $variables = []): string
	{
		$route_prefix = ComponentHelper::getParams('com_apps')->get('route_prefix', 'index.php?option=com_apps&format=json');

		$uri = Uri::getInstance($route_prefix);

		foreach ($variables as $key => $value)
		{
			$uri->setVar($key, $value);
		}

		return $uri->toString();
	}

	/**
	 * Retrieve the JED URL for an extension.
	 *
	 * @param   stdClass  $item  The extension to process
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public static function getJEDUrl($item)
	{
		$url = 'https://extensions.joomla.org/';

		if (!isset($item->id->value))
		{
			return $url;
		}

		$url .= 'index.php?option=com_jed&view=extension&layout=default&id=' . $item->id->value;

		return $url;
	}

	/**
	 * Retrieve the JED URL for an extension category.
	 *
	 * @param   integer  $catid  The JED category ID
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public static function getJEDCatUrl($catid = 0)
	{
		$url = 'https://extensions.joomla.org/';

		if (!$catid)
		{
			return $url;
		}

		$url .= 'index.php?option=com_jed&controller=filter&view=extension&layout=list&Itemid=145&filter[core_catid]=' . $catid;

		return $url;
	}
}
