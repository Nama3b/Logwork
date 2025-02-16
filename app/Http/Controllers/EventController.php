<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEventRequest;
use App\Services\Interfaces\IEventService;
use App\Transformers\EventListTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;

class EventController extends Controller
{
    /**
     * @var IEventService
     */
    protected IEventService $eventService;

    /**
     * @param IEventService $eventService
     */
    public function __construct(IEventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return $this->withErrorHandling(function () {
            $data = $this->eventService->list();

            return $this->response([
                'data' => fractal()->collection($data)
                    ->transformWith(new EventListTransformer())
                    ->serializeWith(new ArraySerializer())
            ]);
        });
    }

    /**
     * @param $id
     * @param SaveEventRequest $request
     * @return JsonResponse
     */
    public function save(SaveEventRequest $request): JsonResponse
    {
        return $this->withErrorHandling(function () use ($id, $request) {
            $data = $this->eventService->save($id, $request);

            return optional($data) ?
                $this->message(__('Lưu lịch thành công!'))->respondCreated() :
                $this->message(__('An unexpected error occurred. Please try again later.'))
                    ->respondBadRequest();
        });
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return $this->withErrorHandling(function () use ($request) {
            $data = $this->eventService->delete($request);

            return optional($data) ?
                $this->message(__('Xóa lịch thành công!'))->respondOk() :
                $this->message(__('An unexpected error occurred. Please try again later.'))
                    ->respondBadRequest();
        });
    }
}
