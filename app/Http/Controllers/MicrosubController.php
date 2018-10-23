<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MicrosubController extends Controller
{
    const CONTROLLER_NAMESPACE = 'App\Http\Controllers\Microsub';
    const AVAILABLE_ACTIONS = [
        'timeline',
        'channels',
        'search',
        'preview',
        'follow', 'unfollow',
        'mute', 'unmute',
        'block', 'unblock',
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function endpoint(Request $request)
    {
        if (in_array($request->action, static::AVAILABLE_ACTIONS)) {
            return $this->{$request->action}($request);
        }

        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function timeline(Request $request)
    {
        if ($request->isMethod('GET')) {
            // TODO add after and before cursors
            return $this->proxy('TimelineController@channel', [$request->query('channel')]);
        }

        if ($request->input('method') === 'mark_read' && $request->input('last_read_entry')) {
            $this->validate($request, [
                'channel' => 'required|string',
                'last_read_entry' => 'string',
            ]);

            return $this->proxy('TimelineController@markEntriesReadFrom', [
                $request->input('channel'),
                $request->input('last_read_entry')
            ]);
        }

        if ($request->input('method') === 'mark_read') {
            $this->validate($request, [
                'channel' => 'required|string',
                'entry' => 'string|array',
            ]);

            return $this->proxy('TimelineController@markEntriesRead', [
                $request->input('channel'),
                $request->input('entry')
            ]);
        }

        if ($request->input('method') === 'mark_unread') {
            $this->validate($request, [
                'channel' => 'required|string',
                'entry' => 'required|string|array',
            ]);

            return $this->proxy('TimelineController@markEntriesUnread', [
                $request->input('channel'),
                $request->input('entry')
            ]);
        }

        return abort(404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    protected function channels(Request $request)
    {
        if ($request->isMethod('GET')) {
            return $this->proxy('ChannelController@index');
        }

        if ($request->input('method') === 'delete') {
            return $this->proxy('ChannelController@delete', [
                $request->input('channel')
            ]);
        }

        if ($request->input('name') && !$request->input('channel')) {
            return $this->proxy('ChannelController@create', [
                $request->input('name')
            ]);
        }

        if ($request->input('name') && $request->input('channel')) {
            return $this->proxy('ChannelController@update', [
                $request->input('channel'),
                $request->input('name')
            ]);
        }

        return abort(404);
    }

    /**
     * @param Request $request
     */
    protected function search(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function preview(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function follow(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function unfollow(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function mute(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function unmute(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function block(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     */
    protected function unblock(Request $request)
    {
        //
    }

    /**
     * @param string $callback
     * @param array $params
     * @return \Illuminate\Http\Response
     */
    private function proxy(string $callback, array $params = [])
    {
        return app()->call(static::CONTROLLER_NAMESPACE . '\\' . $callback, $params);
    }

}
