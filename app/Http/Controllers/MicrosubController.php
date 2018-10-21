<?php

namespace App\Http\Controllers;

use GraphAware\Common\Result\RecordViewInterface;
use Illuminate\Http\Request;

class MicrosubController extends Controller
{
    public function getEndpoint(Request $request)
    {
        switch ($request->action) {
            case "timeline":
                return $this->showTimeline($request);
            case "channels":
                return $this->indexChannels();
        }

        return abort(404);
    }

    public function postEndpoint(Request $request)
    {
        switch ($request->action) {
            case "timeline":
                return $this->timeline($request);
            case "channels":
                return $this->createChannel($request);
        }

        return abort(404);
    }

    private function queryChannels($query) {
        $records = app('neo4j')
            ->run($query)
            ->records();

        return collect($records)
            ->map(function (RecordViewInterface $record) {
                $node = $record->get('chan');
                return [
                    'name' => $node->value('name'),
                    'uid' => $node->value('uid'),
                ];
            });
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

    private function showTimeline(Request $request)
    {
        switch ($request->channel) {
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


    private function indexChannels()
    {
        return response()->json([
            'channels' => [
                [
                    'uid' => 'latest',
                    'name' => 'Latest posts',
                ],
                [
                    'uid' => 'checkins',
                    'name' => 'Checkins',
                ],
                [
                    'uid' => 'liked',
                    'name' => 'Liked posts',
                ]
            ]
        ]);

//        return response()->json([
//            'channels' => $this->queryChannels(
//                'match (chan:Channel)' .
//                'return chan limit 100'
//            ),
//        ]);
    }

    private function createChannel(Request $request)
    {
        $uid = bin2hex(random_bytes(32));

        return response()->json($this->queryChannels(
            "create (chan:Channel {name: '{$request->name}', uid: '$uid'})" .
            'return chan'
        )->first());
    }
}
