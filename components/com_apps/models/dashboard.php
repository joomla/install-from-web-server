<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Exception\CacheExceptionInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

/**
 * Dashboard model.
 *
 * @since  1.0
 */
class AppsModelDashboard extends BaseDatabaseModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.0
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		$ordering = $app->input->get('ordering', 'score');

		$this->setState('list.limit', $app->input->getUint('limit', ComponentHelper::getParams('com_apps')->get('default_limit', 8)));
		$this->setState('list.start', $app->input->getUint('limitstart', 0));
		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $ordering == 'core_title' ? 'ASC' : 'DESC');

		$this->setState('filter.search', str_replace('_', ' ', urldecode(trim($app->input->get('filter_search', null)))));
	}

	/**
	 * Retrieve the base model.
	 *
	 * @return  boolean|AppsModelBase
	 *
	 * @since   1.0
	 */
	private function getBaseModel()
	{
		return BaseDatabaseModel::getInstance('Base', 'AppsModel');
	}

	public function getCategories()
	{
		return $this->getBaseModel()->getCategories();
	}

	public function getBreadcrumbs()
	{
		return $this->getBaseModel()->getBreadcrumbs();
	}

	public function getPluginUpToDate()
	{
		return $this->getBaseModel()->getPluginUpToDate();
	}

	public function getOrderBy(): string
	{
		return $this->getState('list.ordering', 'score');
	}

	public function getExtensions(): array
	{
		/** @var \Joomla\CMS\Cache\Controller\CallbackController $cache */
		$cache = Factory::getCache('com_apps', 'callback');

		// These calls are always cached
		$cache->setCaching(true);

		// Build the request URL here, since this will vary based on params we will use the URL as part of our cache key
		$url = new Uri;

		$url->setScheme('https');
		$url->setHost('extensions.joomla.org');
		$url->setPath('/index.php');
		$url->setVar('option', 'com_jed');
		$url->setVar('controller', 'filter');
		$url->setVar('view', 'extension');
		$url->setVar('format', 'json');
		$url->setVar('limit', $this->getState('list.limit'));
		$url->setVar('limitstart', $this->getState('list.start'));
		$url->setVar('filter[approved]', '1');
		$url->setVar('filter[published]', '1');
		$url->setVar('extend', '0');
		$url->setVar('order', $this->getOrderBy());
		$url->setVar('dir', $this->getState('list.direction'));

		if ($search = $this->getState('filter.search'))
		{
			$url->setVar('searchall', urlencode($search));
		}

		try
		{
			// We explicitly define our own ID to keep the caching API from calculating it separately
			$items = $cache->get([$this, 'fetchDashboardExtensions'], [$url], md5(__METHOD__ . $url->toString()));
		}
		catch (CacheExceptionInterface $e)
		{
			// Cache failure, let's try an HTTP request without caching
			$items = $this->fetchDashboardExtensions($url);
		}
		catch (RuntimeException $e)
		{
			// Other failure, this isn't good
			Log::add(
				'Could not retrieve dashboard extension data from the JED: ' . $e->getMessage(),
				Log::ERROR,
				'com_apps'
			);

			// Throw a "sanitised" Exception but nest the caught Exception for debugging
			throw new RuntimeException('Could not retrieve dashboard extension data from the JED.', $e->getCode(), $e);
		}

		$baseModel = $this->getBaseModel();
		$items     = $items->data;

		// Populate array
		$extensions = [0 => [], 1 => []];

		foreach ($items as $item)
		{
			$item->image = $baseModel->getMainImageUrl($item);

			if ($search)
			{
				$extensions[1 - $item->foundintitle][] = $item;
			}
			else
			{
				$extensions[0][] = $item;
			}
		}

		return array_merge($extensions[0], $extensions[1]);
	}

	/**
	 * Fetches the dashboard extensions from the JED
	 *
	 * @param   Uri  $uri  The URI to request data from
	 *
	 * @return  stdClass
	 *
	 * @since   1.0
	 * @throws  RuntimeException if the HTTP query fails
	 */
	public function fetchDashboardExtensions(Uri $uri)
	{
		try
		{
			$http = $this->getBaseModel()->getHttpClient();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException('Cannot fetch HTTP client to connect to JED', $e->getCode(), $e);
		}

		$response = $http->get($uri->toString());

		// Make sure we've gotten an expected good response
		if ($response->code !== 200)
		{
			throw new RuntimeException('Unexpected response from the JED', $response->code);
		}

		// The body should be a JSON string, if we have issues decoding it assume we have a bad response
		$categoryData = json_decode($response->body);

		if (json_last_error())
		{
			throw new RuntimeException('Unexpected response from the JED, JSON could not be decoded with error: ' . json_last_error_msg(), 500);
		}

		return $categoryData;
	}
}
