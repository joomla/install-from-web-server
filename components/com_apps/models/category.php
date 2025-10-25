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
use Joomla\CMS\Uri\Uri;

JLoader::register('AppsModelBase', __DIR__ . '/base.php');

/**
 * Category model.
 *
 * @since  1.0
 */
class AppsModelCategory extends AppsModelBase
{
	/**
	 * Fetches the category data from the JED
	 *
	 * @param   Uri  $uri  The URI to request data from
	 *
	 * @return  stdClass
	 *
	 * @since   1.0
	 * @throws  RuntimeException if the HTTP query fails
	 */
	public function fetchCategoryExtensions(Uri $uri)
	{
		try
		{
			$http = $this->getHttpClient();
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

	/**
	 * Fetch the breadcrumb tree
	 *
	 * @param   integer|null  $catid  The active category ID
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getBreadcrumbs($catid = null): array
	{
		return parent::getBreadcrumbs($this->getState('filter.id'));
	}

	/**
	 * Fetch the JED categories
	 *
	 * @param   integer|null  $catid  The active category ID
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getCategories($catid = null): array
	{
		return parent::getCategories($this->getState('filter.id'));
	}

	/**
	 * Get the category extensions
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
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
		$url->setVar('filter[core_catid]', $this->getState('filter.id'));
		$url->setVar('extend', '0');
		$url->setVar('order', $this->getOrderBy());
		$url->setVar('dir', $this->getState('list.direction'));

		if ($search = $this->getState('filter.search'))
		{
			$url->setVar('searchall', urlencode($search));
		}

		if ($this->getState('filter.joomla_version') === 'current')
		{
			$joomlaRelease = $this->getState('filter.release');

			// Check for each major version branch, default to 3.x as the oldest version supported by the client is Joomla 3.2
			if (version_compare($joomlaRelease, '6.0', '>='))
			{
				$url->setVar('filter[versions]', '60');
			}
			elseif (version_compare($joomlaRelease, '5.0', '>='))
			{
				$url->setVar('filter[versions]', '50');
			}
			elseif (version_compare($joomlaRelease, '4.0', '>='))
			{
				$url->setVar('filter[versions]', '40');
			}
			else
			{
				$url->setVar('filter[versions]', '30');
			}
		}

		try
		{
			// We explicitly define our own ID to keep the cache API from calculating it separately
			$items = $cache->get([$this, 'fetchCategoryExtensions'], [$url], md5(__METHOD__ . $url->toString()));
		}
		catch (CacheExceptionInterface $e)
		{
			// Cache failure, let's try an HTTP request without caching
			$items = $this->fetchCategoryExtensions($url);
		}
		catch (RuntimeException $e)
		{
			// Other failure, this isn't good
			Log::add(
				'Could not retrieve category extension data from the JED: ' . $e->getMessage(),
				Log::ERROR,
				'com_apps'
			);

			// Throw a "sanitised" Exception but nest the caught Exception for debugging
			throw new RuntimeException('Could not retrieve category extension data from the JED.', $e->getCode(), $e);
		}

		$items = $items->data;

		// Populate array
		$extensions = [0 => [], 1 => []];

		foreach ($items as $item)
		{
			$item->image               = $this->getMainImageUrl($item);
			$item->compatible_versions = $this->getJoomlaVersionCompatibility($item);

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
	 * Get the order by value
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getOrderBy(): string
	{
		return $this->getState('list.ordering', 'score');
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$app = Factory::getApplication();

		$ordering = $app->input->get('ordering', 't2.link_rating');

		$this->setState('list.limit', $app->input->getUint('limit', ComponentHelper::getParams('com_apps')->get('default_limit', 8)));
		$this->setState('list.start', $app->input->getUint('limitstart', 0));
		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $ordering == 'core_title' ? 'ASC' : 'DESC');

		$this->setState('filter.id', $app->input->getUint('id'));
		$this->setState('filter.search', str_replace('_', ' ', urldecode(trim($app->input->get('filter_search', null)))));
	}
}
