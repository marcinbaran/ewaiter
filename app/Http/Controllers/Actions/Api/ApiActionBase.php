<?php

namespace App\Http\Controllers\Actions\Api;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiActionBase extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected $request,
    ) {
        $this->setAuthMiddleware();
    }

    public function __invoke()
    {
        $this->handle();
    }

    abstract public function handle();

    protected function sendResourceResponse(mixed $data, string $resourceClassString, int $status = Response::HTTP_OK): void
    {
        $responseContent = null;

        if ($data instanceof Collection) {
            $responseContent = $resourceClassString::collection($data);
        }

        if ($data instanceof Model) {
            $responseContent = new $resourceClassString($data);
        }

        response($responseContent, $status)->send();
    }

    protected function sendViewResponse(View $view, int $status = Response::HTTP_OK): void
    {
        response($view, $status)->send();
    }

    protected function sendRedirectResponse(RedirectResponse $redirect, int $status = Response::HTTP_OK): void
    {
        response($redirect, $status)->send();
    }

    protected function setAuthMiddleware(): void
    {
        Request::has('provider') && Request::get('provider') == 'tenancy-api' ? $this->middleware(['auth:tenancy-api']) : $this->middleware(['auth:api']);
    }
}
