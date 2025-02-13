<?php

namespace App\Supports;

use Illuminate\Http\JsonResponse;

trait ApiResponseFormatter
{
    /**
     * Format data for API response.
     *
     * @param array $data
     * @param null $customMessage
     * @param null $statusCode
     * @param bool $error
     * @param null $meta
     * @param null $warning
     * @return array
     */
    public function formatDataForApiResponse(
        array $data = [],
        $customMessage = null,
        $statusCode = null,
        bool $error = false,
        $meta = null,
        $warning = null
    ): array {
        $final = [];

        if (!empty($data)) {
            if ($error) {
                $final = array_merge($final, ['errors' => $data]);
            } else {
                $final = array_merge($final, ['data' => $data]);
            }
        }

        $final = array_merge($final, [
            'code' => $statusCode,
            'request_id' => request()->input("x_request_id"),
        ]);

        $message = null;

        if (!empty($customMessage)) {
            $message = $customMessage;
        }

        if ($message) {
            $final = array_merge($final, ['message' => $message]);
        }

        if ($warning) {
            $final = array_merge($final, ['warning' => $warning]);
        }

        if ($meta) {
            $final = array_merge($final, ['meta' => $meta]);
        }

        return $final;
    }

    /**
     * @param $request
     * @param $exception
     * @param int $status
     * @param null $message
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function prepareErrorResponse(
        $request,
        $exception,
        int $status = 400,
        $message = null,
        array $data = [],
        array $headers = []
    ): JsonResponse {
        if (is_null($message) && method_exists($exception, 'getMessage')) {
            $message = $exception->getMessage();
        }

        if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() !== null) {
            $status = $exception->getStatusCode();
        }

        return $this->respondError(
            $data,
            __($message),
            $status,
            $headers
        );
    }

    /**
     * @param array $data
     * @param null $message
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function respondError(array $data = [], $message = null, int $status = 400, array $headers = []): JsonResponse
    {
        $data = $this->formatDataForApiResponse($data, $message, $status, true);

        return response()->json($data, $status, $headers);
    }
}
