<?php

namespace App\Services;

use GuzzleHttp\Client;

class XRay {

    /**
     * @var Client
     */
    private $client;

    /**
     * XRay constructor.
     * @param Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * @param string  $url
     * @param bool $feed
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetch($url, $feed = false) {
        $url = $this->buildUrl($url, $feed);

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Call failed');
        }

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @param $url
     * @param $feed
     * @return string
     */
    protected function buildUrl($url, $feed): string {
        $url = config('app.xray') . '/parse?url=' . urlencode($url);

        if ($feed) $url .= '&expect=feed';

        if (str_start($url, 'https://twitter.com')) {
            $url .= '&twitter_api_key=' . config('app.twitter-api-key');
            $url .= '&twitter_api_secret=' . config('app.twitter-api-secret');
            $url .= '&twitter_access_token=' . config('app.twitter-access-token-key');
            $url .= '&twitter_access_token_secret=' . config('app.twitter-access-token-secret');
        }

        return $url;
    }
}
