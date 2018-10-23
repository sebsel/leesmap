<?php

namespace App\Http\Controllers\Microsub;

use App\Http\Controllers\Controller;
use GraphAware\Common\Result\RecordViewInterface;

class ChannelController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
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
    }

    /**
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(string $name)
    {
        $uid = bin2hex(random_bytes(32));

        return response()->json($this->queryChannels(
            "create (chan:Channel {name: '{$name}', uid: '$uid'})" .
            'return chan'
        )->first());
    }

    /**
     * @param string $channel
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $channel, string $name)
    {
        //
    }

    /**
     * @param string $channel
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(string $channel)
    {
        //
    }

    /**
     * @param string[] $channels
     * @return \Illuminate\Http\JsonResponse
     */
    public function order(array $channels)
    {
        //
    }

    /**
     * @param $query
     * @return \Illuminate\Support\Collection
     */
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
}
