<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveAuthor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $item;

    /**
     * Create a new job instance.
     *
     * @param $item
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!isset($this->item['url'])) return;

        $existing = app('neo4j')
            ->run("match (ent:Author {url: \"{$this->item['url']}\"}) return ent")
            ->records();

        if (count($existing)) return;

        $json = addslashes(json_encode($this->item));
        app('neo4j')->run(
            "create (ent:Author { 
                url: \"{$this->item['url']}\",
                name: \"{$this->item['name']}\",
                content: \"$json\"
            })"
        );
    }
}
