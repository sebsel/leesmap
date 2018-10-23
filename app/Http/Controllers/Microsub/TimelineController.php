<?php

namespace App\Http\Controllers\Microsub;

use App\Http\Controllers\Controller;
use GraphAware\Common\Result\RecordViewInterface;

class TimelineController extends Controller
{
    /**
     * @param string $channel
     * @return \Illuminate\Http\JsonResponse
     */
    public function channel(string $channel)
    {
        switch ($channel) {
            case "liked":
                $query = "match (entry:Entry)-[:LIKES]->(liked:Entry) " .
                    'with liked, count(entry) as likes ' .
                    'return liked as ent order by likes desc, ent.published desc limit 100';
                break;
            case "latest":
                $query = 'match (ent:Entry) ' .
                    'return ent order by ent.published desc limit 100';
                break;
            case "checkins":
                $query = 'match (ent:Entry {post_type: "checkin"}) ' .
                    'return ent order by ent.published desc limit 100';
                break;
            default:
                return response()->json(['error' => 'channel not found']);
        }

        return response()->json([
            'items' => $this->queryEntries($query),
            'paging' => [
                'before' => 'xxx',
                'after' => 'xxx'
            ]
        ]);
    }

    /**
     * @param string $channel
     * @param string|string[] $entries
     * @return \Illuminate\Http\JsonResponse
     */
    public function markEntriesRead(string $channel, $entries)
    {
        if (!is_array($entries)) $entries = [$entries];

        //
    }

    /**
     * @param string $channel
     * @param string|string[] $entries
     * @return \Illuminate\Http\JsonResponse
     */
    public function markEntriesUnread(string $channel, $entries)
    {
        if (!is_array($entries)) $entries = [$entries];

        //
    }

    /**
     * @param string $channel
     * @param string $lastReadEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function markEntriesReadFrom(string $channel, string $lastReadEntry)
    {
        //
    }

    /**
     * @param string $channel
     * @param $entries
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeEntries(string $channel, $entries)
    {
        if (!is_array($entries)) $entries = [$entries];

        //
    }

    private function queryEntries($query) {
        $records = app('neo4j')
            ->run($query)
            ->records();

        return collect($records)
            ->map(function (RecordViewInterface $record) {
                $node = $record->get('ent');
                return json_decode($node->value('content'), true);
            });
    }
}
