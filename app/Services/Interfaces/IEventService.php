<?php

namespace App\Services\Interfaces;

interface IEventService
{
    public function list();

    public function save($request);

    public function delete($request);
}
