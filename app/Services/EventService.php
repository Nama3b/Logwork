<?php

namespace App\Services;

use App\Services\Interfaces\IEventService;

class EventService implements IEventService
{
    public function list()
    {

    }

    public function save($id, $request)
    {
        dd($request);
    }

    public function delete($id)
    {
        // TODO: Implement create() method.
    }
}
