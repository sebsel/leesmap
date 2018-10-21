<?php

namespace App\Jobs;

use App\Services\XRay;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $url;

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $body = app(XRay::class)->fetch($this->url, true);

        if (!isset($body['data']['type'])) return;
        if ($body['data']['type'] != 'entry') return;

        SaveEntry::dispatchNow($body['data']);

        if (isset($body['data']['author']['url']) && isset($body['data']['url'])) {
            SaveAuthor::dispatchNow($body['data']['author']);
            SaveRelation::dispatchNow($body['data']['author']['url'], $body['data']['url'], 'author-of');
        }
    }
}
