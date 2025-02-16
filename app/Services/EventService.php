<?php

namespace App\Services;

use App\Models\Event;
use App\Services\Interfaces\IEventService;
use Carbon\Carbon;

class EventService implements IEventService
{
    public function list()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return Event::where('month', $currentMonth)->where('year', $currentYear)->get();
    }

    public function save($id, $request)
    {

    }

    public function delete($request)
    {
        $query = Event::where('id', $request->id)->first();


    }
}
