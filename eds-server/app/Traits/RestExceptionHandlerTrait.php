<?php
/**
 * Created by PhpStorm.
 * User: fisherman
 * Date: 2019-11-04
 * Time: 22:38
 */

namespace App\Traits;


namespace App\Traits;

use App\Enums\ReasonCodeValues;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;


trait RestExceptionHandlerTrait
{
    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
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

        return $this->badRequest($e->getMessage());
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message = 'Bad request', $statusCode = 400)
    {
        return $this->jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    /**
     *
     * @param string $message
     * @param array $data
     * @param int $statusCode for validation we could either give status code 200 or 422 for client easiest handling we giving 200
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRquestUnprocessableEntity($message = 'Invalid data provided', $data = [], $statusCode = 200)
    {
        return $this->jsonResponse(['success' => false, 'rc' => ReasonCodeValues::VALIDATION_FAILED, 'message' => $message, 'data' => $data], $statusCode);
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param Exception $e
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequestWithDetails($e, $statusCode = 400)
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
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message = 'Not found', $statusCode = 404)
    {
        return $this->jsonResponse(['success' => false, 'message' => $message], $statusCode);
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = 404)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * @param \Illuminate\Validation\ValidationException $e
     * @return array
     */
    protected function formatValidationErrorsFromException(ValidationException $e)
    {
        $errors = $e->validator->errors();

        return [$e->getMessage(), $errors->toArray()];
    }
}
