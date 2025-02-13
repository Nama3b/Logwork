<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEventRequest;
use App\Services\Interfaces\IEventService;
use App\Transformers\EventListTransformer;
use Illuminate\Http\JsonResponse;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
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
                    ->paginateWith(new IlluminatePaginatorAdapter($data))
            ]);
        });
    }

    /**
     * @param $id
     * @param SaveEventRequest $request
     * @return JsonResponse
     */
    public function save($id, SaveEventRequest $request): JsonResponse
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
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        return $this->withErrorHandling(function () use ($id) {
            $data = $this->eventService->delete($id);

            return optional($data) ?
                $this->message(__('Xóa lịch thành công!'))->respondOk() :
                $this->message(__('An unexpected error occurred. Please try again later.'))
                    ->respondBadRequest();
        });
    }
}
