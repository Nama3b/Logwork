<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class EventListTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param $data
     * @return array
     */
    public function transform($data): array
    {
        return [
            'id' => $data->id,
            'day' => $data->day,
            'month' => $data->month,
            'year' => $data->year,
            'time_slot' => $data->time_slot,
            'detail' => $data->detail
        ];
    }

}
