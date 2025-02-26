<?php

namespace App\Exceptions;

use App\Exceptions\ApiExceptions\ValidationException as CustomValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait ExceptionHandlerTrait
{
    /**
     * Creates a new redirect response based on exception type.
     *
     * @param Request   $request
     * @param Exception $e
     *
     * @return Response|RedirectResponse
     */
    protected function getResponseForException(Request $request, \Throwable $e)
    {
        if ($this->isAccessDeniedHttpException($e)) {
            session()->flash('alert-danger', $e->getMessage());

            return redirect()->back();
        }

        return parent::render($request, $e);
    }

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request   $request
     * @param Exception $e
     *
     * @return JsonResponse
     */
    protected function getJsonResponseForException(Request $request, \Throwable $e): JsonResponse|\Throwable
    {
        if ($this->isModelNotFoundException($e)) {
            return $this->jsonResponse(['error' => 'Record not found', 'details' => null, 'data' => null, 'locale' => null], 404);
        }

        if ($this->isValidationException($e)) {
            $errorArray = is_array($e->errors()) ? $e->errors() : [$e->errors()];

            return new CustomValidationException($errorArray);
        }

        if ($this->isDatabaseQueryException($e)) {
            return $this->jsonResponse(['error' => 'Query faild', 'details' => $e->getMessage(), 'data' => null, 'locale' => null], 400);
        }

        if ($this->isAuthenticationException($e)) {
            return $this->jsonResponse(['error' => $e->getMessage(), 'details' => null, 'data' => null, 'locale' => null], 401);
        }

        if ($this->isAccessDeniedHttpException($e)) {
            return $this->jsonResponse(['error' => $e->getMessage(), 'details' => null, 'data' => null, 'locale' => null], 403);
        }

        return $e;

        return $this->jsonResponse(['error' => $e->getMessage(), 'trace' => $e->getFile().'  '.$e->getLine(), 'other' => $e->getTraceAsString(), 'details' => null, 'data' => null, 'locale' => null], $e->getCode() ?: 400);
    }

    /**
     * Determines if request is an api call.
     *
     * If the request URI contains '/api/'.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function isApiCall(Request $request): bool
    {
        return false !== strpos($request->getUri(), '/api/') || $request->wantsJson();
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int        $statusCode
     *
     * @return JsonResponse
     */
    protected function jsonResponse(array $payload = null, int $statusCode = 404): JsonResponse
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     *
     * @return bool
     */
    protected function isModelNotFoundException(\Throwable $e): bool
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Determines if the given exception is a request validation faild.
     *
     * @param Exception $e
     *
     * @return bool
     */
    protected function isValidationException(\Throwable $e): bool
    {
        return $e instanceof ValidationException;
    }

    /**
     * Determines if the given exception is a datatbase query faild.
     *
     * @param Exception $e
     *
     * @return bool
     */
    protected function isDatabaseQueryException($e): bool
    {
        return $e instanceof QueryException;
    }

    /**
     * Determines if the given exception is a datatbase query faild.
     *
     * @param Exception $e
     *
     * @return bool
     */
    protected function isAuthenticationException($e): bool
    {
        return $e instanceof AuthenticationException;
    }

    /**
     * Determines if the given exception is an access denied.
     *
     * @param Exception $e
     *
     * @return bool
     */
    protected function isAccessDeniedHttpException($e): bool
    {
        return $e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException;
    }

    public function subArraysToString($ar, $sep = ', ')
    {
        $str = '';
        foreach ($ar as $val) {
            $str .= implode($sep, $val);
            $str .= $sep; // add separator between sub-arrays
        }
        $str = rtrim($str, $sep); // remove last separator

        return $str;
    }
}
