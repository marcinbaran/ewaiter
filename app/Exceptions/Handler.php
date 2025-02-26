<?php

namespace App\Exceptions;

use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\View\ViewException;

class Handler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    public function render($request, \Throwable $e)
    {
        if ($e instanceof ViewException) {
            $e = $e->getPrevious();
        }

        if (app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }
        if ($request->wantsJson() || $this->isApiCall($request)) {
            $response = $this->getJsonResponseForException($request, $e);

            if ($response instanceof ApiException) {
                return $response->render($request);
            }

            if ($response instanceof \Throwable) {
                return parent::render($request, $e);
            }

            return $response;
        }

        $host = app('request')->server()['HTTP_HOST'] ?? '';
        if ($e instanceof AuthenticationException) {
            return redirect()->response(route($e->redirectTo()))->with('alert-danger', $e->getMessage());
        } elseif ($e instanceof ValidationException) {
            $messages = [];
            foreach ($e->errors() as $error) {
                $messages[] = $error[0];
            }
            $request->session()->flash('alert-danger', implode('<br>', $messages));
        } elseif ($e instanceof SimpleValidationException) {
            $request->session()->flash('alert-danger', implode('<br>', $e->errors()));

            return redirect()->back()->withInput();
        } elseif ($e instanceof AuthorizationException) {
            return redirect()->route('admin.auth.login');
        } elseif ($host == 'localhost:8002' && $e->getCode() != 0) {
//            dd($e->getCode());
        }

        return parent::render($request, $e);
    }
}
