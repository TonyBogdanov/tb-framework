<?php
/**
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */

namespace TB\Framework\Util;

use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Wa72\HtmlPageDom\HtmlPageCrawler as DOM;

class TwitterBootstrap
{
    /**
     * HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Default path ignore patterns.
     *
     * @var array
     */
    protected $defaultIgnore = [
        '/getting-started',
        '/about',
        '/migration',
        '/layout',
        '/content/reboot'
    ];

    /**
     * The base URL of the example site.
     * All anchors will be relative to this.
     *
     * @return string
     */
    protected function getBaseUrl()
    {
        return 'https://v4-alpha.getbootstrap.com';
    }

    /**
     * Get the entry point URL (minus the base URL) from where to parse the navigation.
     *
     * @return string
     */
    protected function getEntryPointUrl()
    {
        return '/getting-started/introduction/';
    }

    /**
     * Fetch the contents of the specified URL using a GET request or throw an exception.
     *
     * @param string $url
     * @param int $expectedStatusCode
     * @return string
     * @throws \Exception
     */
    protected function fetch($url, $expectedStatusCode = 200)
    {
        if (!isset($this->client)) {
            $this->client = new Client();
        }

        $result = $this->client->get($url);
        if ($expectedStatusCode !== $result->getStatusCode()) {
            throw new \Exception(ExceptionHelper::format('Could not fetch: :url, expected status code:' .
                ' :expected, got: :actual (:reason).', [
                'url' => $url,
                'expected' => $expectedStatusCode,
                'actual' => $result->getStatusCode(),
                'reason' => $result->getReasonPhrase()
            ]));
        }

        return (string) $result->getBody();
    }

    /**
     * Fetch the contents of the specified URL and convert it to a DOM object.
     *
     * @param string $url
     * @param int $expectedStatusCode
     * @return DOM
     */
    protected function fetchDOM($url, $expectedStatusCode = 200)
    {
        return DOM::create($this->fetch($url, $expectedStatusCode));
    }

    /**
     * Generate the example style guide output.
     *
     * @param array|null $ignore
     * @return string
     * @throws \Exception
     */
    protected function generateExample(array $ignore = null)
    {
        $result = '';
        $ignore = isset($ignore) ? $ignore : $this->defaultIgnore;

        // fetch navigation
        $entry = $this->fetchDOM($this->getBaseUrl() . $this->getEntryPointUrl());
        $nav = $entry->filter('#docsNavbarContent');
        if (1 !== count($nav)) {
            throw new \Exception('Could not find navigation from entry point.');
        }
        $nav = $nav->first();

        // fetch & parse entries
        /** @var DOM $anchor */
        $nav->filter('a')->each(function ($anchor) use (&$result, $ignore) {
            $href = (string) $anchor->attr('href');

            foreach ($ignore as $path) {
                if ($path === substr($href, 0, strlen($path))) {
                    return;
                }
            }

            $page = $this->fetchDOM($this->getBaseUrl() . $anchor->attr('href'));

            $contentHeading = $page->filter('h2#contents');
            if (0 < count($contentHeading)) {
                $contentMenu = $contentHeading->nextAll()->filter('ul');
                if (0 < count($contentMenu)) {
                    $contentMenu->remove();
                }
                $contentHeading->remove();
            }
            
            $result .= $page->filter('.bd-content')->first()->html();
        });

        return '<div class="container">' . $result . '</div>';
    }

    /**
     * Fetch the example markup and potentially cache it.
     *
     * @param bool $forceFresh
     * @param array|null $ignore
     * @return mixed|null|string
     */
    public function fetchExample($forceFresh = false, array $ignore = null)
    {
        $cache = new FilesystemCache('tb.twitter_bootstrap');
        if ($forceFresh) {
            $cache->clear();
        }

        if ($cache->has('example')) {
            return $cache->get('example');
        }

        $cache->set('example', $content = $this->generateExample($ignore));
        return $content;
    }

    /**
     * Output the example markup.
     *
     * @param bool $forceFresh
     * @param array|null $ignore
     */
    public function printExample($forceFresh = false, array $ignore = null)
    {
        echo $this->fetchExample($forceFresh, $ignore);
    }
}