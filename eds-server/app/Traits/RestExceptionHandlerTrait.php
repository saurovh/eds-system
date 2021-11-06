<?php
/**
 * Created by PhpStorm.
 * User: fisherman
 * Date: 2019-11-04
 * Time: 22:38
 */

namespace App\Traits;

namespace App\Traits;

use App\Enums\HttpResponseStatus;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

trait RestExceptionHandlerTrait
{
    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request   $request
     * @param Exception $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->modelNotFound();
        }

        if ($e instanceof ValidationException) {
            if (app()->runningUnitTests()) {
                return $this->badRequestWithDetails($e);
            }
            list($message, $data) = $this->formatValidationErrorsFromException($e);
            return $this->badRquestUnprocessableEntity($message, $data);
        }
        if ($e instanceof AuthorizationException || $e instanceof AuthenticationException) {
            return $this->badRequest($e->getMessage(), HttpResponseStatus::HTTP_UNAUTHORIZED);
        }

        return $this->badRequest($e->getMessage(), HttpResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int    $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message = 'Bad request', $statusCode = HttpResponseStatus::HTTP_BAD_REQUEST)
    {
        return $this->jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    /**
     * @param string $message
     * @param array  $data
     * @param int    $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRquestUnprocessableEntity($message = 'Invalid data provided', $data = [], $statusCode = HttpResponseStatus::HTTP_METHOD_NOT_ALLOWED)
    {
        return $this->jsonResponse(['message' => $message, 'data' => $data], $statusCode);
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param Exception $e
     * @param int       $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequestWithDetails($e, $statusCode = HttpResponseStatus::HTTP_BAD_REQUEST)
    {
        $data = array_map(function ($item) {
            return implode(", ", $item);
        }, $e->validator->getMessageBag()->toArray());

        $data['error'] = $this->formatValidationErrorsFromException($e);

        return $this->jsonResponse($data, $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @param int    $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message = 'Not found', $statusCode = HttpResponseStatus::HTTP_NOT_FOUND)
    {
        return $this->jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int        $statusCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = HttpResponseStatus::HTTP_NOT_FOUND)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * @param \Illuminate\Validation\ValidationException $e
     *
     * @return array
     */
    protected function formatValidationErrorsFromException(ValidationException $e)
    {
        $errors = $e->validator->errors();

        return [$e->getMessage(), $errors->toArray()];
    }
}
