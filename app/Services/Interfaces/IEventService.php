<?php

namespace App\Services\Interfaces;

interface IEventService
{
    public function list();

    public function save($id, $request);

    public function delete($id);
}
