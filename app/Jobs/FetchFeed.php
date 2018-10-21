<?php

namespace App\Jobs;

use App\Services\XRay;
use GraphAware\Neo4j\Client\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class FetchFeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url;

    /**
     * Create a new job instance.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $body = app(XRay::class)->fetch($this->url, true);

        if (!isset($body['data']['items']) || !is_array($body['data']['items'])) {
            throw new \Exception('no feed found');
        }

        foreach ($body['data']['items'] as $item) {

            SaveEntry::dispatchNow($item);
            if (isset($item['author']['url']) && isset($item['url'])) {
                SaveAuthor::dispatchNow($item['author']);
                SaveRelation::dispatchNow($item['author']['url'], $item['url'], 'author-of');
            }

            if (isset($item['like-of'])) {
                foreach ($item['like-of'] as $like) {
                    FetchEntry::dispatchNow($like);
                    SaveRelation::dispatchNow($item['url'], $like, 'like-of');
                }
            }
        }
    }

//    private function serialize($item, $assoc = true) {
//        $output = [];
//
//        foreach ($item as $key => $value) {
//            $string = $assoc ? str_replace('-', '_', $key) . ':' : '';
//
//            if (is_array($value) && Arr::isAssoc($value)) {
//                $string .= $this->serialize($value, true);
//            } elseif (is_array($value)) {
//                $string .= $this->serialize($value, false);
//            } elseif (is_numeric($value)) {
//                $string .= (string) $value;
//            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}.\d{2}:\d{2}/', $value)) {
//                $string .= 'datetime("' . $value . '")';
//            } else {
//                $string .= '"' . (string)$value . '"';
//            }
//
//            $output[] = $string;
//        }
//
//        $output = implode(',', $output);
//
//        return $assoc ? '{' . $output . '}' : '[' . $output . ']';
//    }
}
