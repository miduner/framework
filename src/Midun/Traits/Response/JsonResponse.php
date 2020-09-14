<?php

namespace Midun\Traits\Response;

trait JsonResponse
{
    /**
     * Return generic json response with the given data.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return void
     */
    protected function respond($data, int $statusCode = 200, array $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Respond with created.
     *
     * @param mixed $data
     * @return void
     */
    protected function respondCreated($data)
    {
        return $this->respond($data, 201);
    }

    /**
     * Respond with success.
     *
     * @param $data
     * @param int $statusCode
     * @return void
     */
    protected function respondSuccess($data, int $statusCode = 200)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,

        ], $statusCode);
    }

    /**
     * Respond with error.
     *
     * @param $message
     * @param $statusCode
     * @return void
     */
    protected function respondError(string $message = 'Bad request', int $statusCode = 400)
    {
        return $this->respond([
            'success' => false,
            'errors' => [
                'message' => $message,
            ],
        ], $statusCode);
    }

    /**
     * Respond with no content.
     *
     * @return void
     */
    protected function respondNoContent()
    {
        return $this->respondSuccess(null, 204);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     * @return void
     */
	protected function respondUnauthorized(string $message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     * @return void
     */
    protected function respondForbidden(string $message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    /**
	 * Respond with not found.
     *
     * @param string $message
     * @return void
     */
	protected function respondNotFound(string $message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @return void
     */
    protected function respondInternalError(string $message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }
}
