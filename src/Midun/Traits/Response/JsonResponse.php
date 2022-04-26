<?php

namespace Midun\Traits\Response;

use Midun\Supports\Response\Response;

trait JsonResponse
{
    /**
     * Return generic json response with the given data.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    protected function respond($data, int $statusCode = 200, array $headers = []): Response
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Respond with created.
     *
     * @param mixed $data
     * @return Response
     */
    protected function respondCreated($data): Response
    {
        return $this->respond($data, 201);
    }

    /**
     * Respond with success.
     *
     * @param $data
     * @param int $statusCode
     * @return Response
     */
    protected function respondSuccess($data, int $statusCode = 200): Response
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
     * @return Response
     */
    protected function respondError(string $message = 'Bad request', int $statusCode = 400): Response
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
     * @return Response
     */
    protected function respondNoContent(): Response
    {
        return $this->respondSuccess(null, 204);
    }

    /**
     * Respond with unauthorized.
     *
     * @param string $message
     * @return Response
     */
	protected function respondUnauthorized(string $message = 'Unauthorized'): Response
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     * @return Response
     */
    protected function respondForbidden(string $message = 'Forbidden'): Response
    {
        return $this->respondError($message, 403);
    }

    /**
	 * Respond with not found.
     *
     * @param string $message
     * @return Response
     */
	protected function respondNotFound(string $message = 'Not Found'): Response
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @return Response
     */
    protected function respondInternalError(string $message = 'Internal Error'): Response
    {
        return $this->respondError($message, 500);
    }
}
