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

    public function save($request)
    {
        $query = Event::query();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        if (!$request->id) {
            $data = [
                'day' => $request->day,
                'month' => $currentMonth,
                'year' => $currentYear,
                'time_slot' => $request->time_slot,
                'detail' => $request->detail
            ];

            $query->insert($data);
        } else {
            $query->where('id', $request->id)->first();

            $data = [
                'detail' => $request->detail
            ];

            $query->upsert($data, ['day', 'month', 'year'], 'detail');
        }

        return $query;
    }

    public function delete($request)
    {
        $query = Event::where('id', $request->id)->first();

        if (!empty($query)) {
            $query->delete();
        }

        return $query;
    }
}
