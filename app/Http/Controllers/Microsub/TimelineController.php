<?php

namespace App\Http\Controllers\Microsub;

use App\Http\Controllers\Controller;

class TimelineController extends Controller
{
    public function channel(string $channel)
    {
        //
    }

    public function markEntriesRead(string $channel, $entries)
    {
        if (!is_array($entries)) $entries = [$entries];

        //
    }

    public function markEntriesReadFrom(string $channel, string $lastReadEntry)
    {
        //
    }

    public function removeEntries(string $channel, $entries)
    {
        if (!is_array($entries)) $entries = [$entries];

        //
    }
}
