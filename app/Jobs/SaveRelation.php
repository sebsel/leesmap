<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveRelation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $from;
    private $to;
    private $type;

    /**
     * Create a new job instance.
     *
     * @param $from
     * @param $to
     * @param $type
     */
    public function __construct($from, $to, $type)
    {
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->type) {
            case "like-of":
                $type = "LIKES";
                break;
            case "bookmark-of":
                $type = "BOOKMARKED";
                break;
            case "author-of":
                $type = "AUTHOR_OF";
                break;
            default:
                $type = "MENTIONS";
                break;
        }

        app('neo4j')->run(
            "match (from {url: \"{$this->from}\"}), (to {url: \"{$this->to}\"})
            create (from)-[:$type]->(to)"
        );
    }
}
